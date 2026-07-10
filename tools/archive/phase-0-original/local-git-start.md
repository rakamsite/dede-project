# Local Git start for XAMPP

Local path example:

```powershell
cd D:\xampp\htdocs\dede
```

Copy these files/folders into the local WordPress root:

- `.gitignore`
- `README.md`
- `docs/`
- `tools/`

Then run:

```powershell
git init
git add .gitignore README.md docs tools wp-content/themes/DeDeTemPlate wp-content/plugins/DeDeV1 wp-content/plugins/DeDeV2
git status
git commit -m "chore: initialize DeDe project repository"
```

Connect to GitHub:

```powershell
git branch -M main
git remote add origin https://github.com/YOUR_USER/YOUR_REPO.git
git push -u origin main
```

From now on, edit files directly inside XAMPP. WordPress sees changes immediately; Git only records them.
