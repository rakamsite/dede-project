<?php

if (!defined('ABSPATH')) {
    exit;
}

trait DeDe_Store_Features_Location
{
    private function get_state_records()
    {
        static $records;
        if (null !== $records) {
            return $records;
        }
        $records = array();
        if (!function_exists('WC') || !WC()->countries) {
            return $records;
        }

        $terms = taxonomy_exists('city_country') ? get_terms(array(
            'taxonomy' => 'city_country',
            'parent' => 0,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        )) : array();
        if (is_wp_error($terms)) {
            $terms = array();
        }

        $terms_by_name = array();
        foreach ($terms as $term) {
            $terms_by_name[$this->normalize_persian($term->name)] = $term;
        }
        foreach ((array) WC()->countries->get_states('IR') as $code => $name) {
            $term = $terms_by_name[$this->normalize_persian($name)] ?? null;
            $records[$code] = array(
                'code' => $code,
                'name' => $name,
                'term_id' => $term ? (int) $term->term_id : 0,
            );
        }
        return $records;
    }

    private function state_record($code)
    {
        $states = $this->get_state_records();
        return $states[$code] ?? null;
    }

    private function get_cities_for_state($state_code)
    {
        $state = $this->state_record($state_code);
        if (!$state || !$state['term_id'] || !taxonomy_exists('city_country')) {
            return array();
        }
        $terms = get_terms(array(
            'taxonomy' => 'city_country',
            'parent' => $state['term_id'],
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        ));
        if (is_wp_error($terms)) {
            return array();
        }
        return array_map(static function ($term) {
            return array('id' => (int) $term->term_id, 'name' => $term->name);
        }, $terms);
    }

    private function city_belongs_to_state($city_id, $state_code)
    {
        $state = $this->state_record($state_code);
        $city = get_term($city_id, 'city_country');
        return $state && $state['term_id'] && $city && !is_wp_error($city)
            && (int) $city->parent === (int) $state['term_id'];
    }

    private function normalize_persian($value)
    {
        $value = str_replace(array('ي', 'ى', 'ك', 'ۀ', 'ة'), array('ی', 'ی', 'ک', 'ه', 'ه'), (string) $value);
        $value = preg_replace('/[\x{200C}\s]+/u', '', $value);
        return function_exists('mb_strtolower') ? mb_strtolower(trim($value), 'UTF-8') : trim($value);
    }
}
