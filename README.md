
# KaterinQ 

Tutorial Instalasi Project KaterinQ


## Fitur yang tersedia

- Light/dark mode toggle
- Terintegrasi dengan Duitku Sandbox
- Multi Level (User as Customer, Vendor as Merchant, and Admin as Master Admin)
- Sistem Cart



## Cara Instalasi

```
1. git clone https://github.com/nuzulr24/katerinq.git
2. buat database terlebih dahulu dan unggah file sql yang ada di folder db/projects.sql
3. cp .env.example .env
4. ubah data .env terlebih dahulu
5. php artisan storage:link
6. php artisan key:generate
3. php artisan serve
```

## Akun Pengguna
```
Administrator:
Email: admin@katerinq.com
Password: 123456
-------------------------------
User:
Email: andi@gmail.com
Password: 12345678
-------------------------------
Merchant:
Email: toni@gmail.com
Password: 12345678
```

## Uji Coba Pembayaran Third Party Duitku
Pada saat melakukan checkout dan nantinya akan diarahkan ke laman pembayaran silahkan masukkan nomer dibawah ini
```
Kode: 4000 0000 0000 0044	
Expire: 03/33
CVV: 123
```

## License

[MIT](https://choosealicense.com/licenses/mit/) Segala project yang dikerjakan merupakan hak cipta dari Nuzul

