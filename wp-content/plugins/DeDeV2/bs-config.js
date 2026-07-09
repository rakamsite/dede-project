module.exports = {
    proxy: "127.0.0.100/dede/",
    // https:true,
    files: [
        "**/*.php",
        "**/*.css",
        "**/*.js",
    ],
    reloadDelay: 0,
    notify: false
};