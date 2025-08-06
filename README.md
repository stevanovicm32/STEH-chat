# Chat Aplikacija - Laravel

Real-time chat aplikacija napravljena u Laravel framework-u sa WebSocket podrškom za uživo dopisivanje.

## Funkcionalnosti

- **Autentifikacija korisnika** - registracija, prijava, odjava
- **Upravljanje sobama** - kreiranje javnih i privatnih soba
- **Real-time poruke** - slanje i primanje poruka kroz WebSocket
- **Online status** - praćenje online/offline statusa korisnika
- **Uloge korisnika** - admin, moderator, član
- **REST API** - potpuna API dokumentacija

## Tehnologije

- **Backend**: Laravel 12
- **Baza podataka**: SQLite
- **WebSocket**: Pusher
- **Autentifikacija**: Laravel Sanctum
- **Testiranje**: PHPUnit

## Instalacija

1. **Kloniranje repozitorijuma**
```bash
git clone https://github.com/stevanovicm32/STEH-chat.git
cd STEH-chat/chat-aplikacija
```

2. **Instalacija zavisnosti**
```bash
composer install
```

3. **Konfiguracija okruženja**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfiguracija baze podataka**
```bash
# SQLite je već konfigurisan u .env fajlu
touch database/database.sqlite
```

5. **Pokretanje migracija i seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Pokretanje aplikacije**
```bash
php artisan serve
```

## API Dokumentacija

### Autentifikacija

#### Registracija
```
POST /api/register
Content-Type: application/json

{
    "name": "Marko Stevanović",
    "email": "marko@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Prijava
```
POST /api/login
Content-Type: application/json

{
    "email": "marko@example.com",
    "password": "password123"
}
```

#### Odjava
```
POST /api/logout
Authorization: Bearer {token}
```

### Sobe

#### Prikaz svih javnih soba
```
GET /api/sobe
Authorization: Bearer {token}
```

#### Kreiranje nove sobe
```
POST /api/sobe
Authorization: Bearer {token}
Content-Type: application/json

{
    "naziv": "Opšta diskusija",
    "opis": "Opšta soba za razgovor",
    "je_javna": true,
    "maksimalan_broj_clanova": 100
}
```

#### Prikaz određene sobe
```
GET /api/sobe/{id}
Authorization: Bearer {token}
```

#### Pridruživanje sobi
```
POST /api/sobe/{id}/pridruzi-se
Authorization: Bearer {token}
```

#### Napuštanje sobe
```
DELETE /api/sobe/{id}/napusti
Authorization: Bearer {token}
```

### Poruke

#### Prikaz poruka u sobi
```
GET /api/sobe/{sobaId}/poruke
Authorization: Bearer {token}
```

#### Slanje poruke
```
POST /api/poruke
Authorization: Bearer {token}
Content-Type: application/json

{
    "soba_id": 1,
    "sadrzaj": "Zdravo svima!",
    "tip_poruke": "tekst"
}
```

#### Ažuriranje poruke
```
PUT /api/poruke/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "sadrzaj": "Ažurirana poruka"
}
```

#### Brisanje poruke
```
DELETE /api/poruke/{id}
Authorization: Bearer {token}
```

## WebSocket Events

### Nova Poruka
```javascript
// Slušanje novih poruka u sobi
Echo.channel('soba.' + sobaId)
    .listen('nova-poruka', (e) => {
        console.log('Nova poruka:', e);
    });
```

### Online Status
```javascript
// Slušanje promena online statusa
Echo.channel('online-status')
    .listen('korisnik-status', (e) => {
        console.log('Status korisnika:', e);
    });
```

## Test Podaci

Aplikacija dolazi sa predefinisanim test podacima:

### Korisnici
- **Marko Stevanović** (marko@example.com) - password123
- **Ana Petrović** (ana@example.com) - password123  
- **Petar Jovanović** (petar@example.com) - password123

### Sobe
- **Opšta diskusija** - javna soba
- **Programiranje** - javna soba
- **Privatna soba** - privatna soba

## Struktura Baze Podataka

### Tabele
- `users` - korisnici aplikacije
- `sobas` - chat sobe
- `porukas` - poruke u sobama
- `clan_sobes` - članovi soba sa ulogama

### Odnosi
- User ↔ Poruka (1:N)
- User ↔ ClanSobe (1:N)
- Soba ↔ Poruka (1:N)
- Soba ↔ ClanSobe (1:N)
- User ↔ Soba (N:N kroz ClanSobe)

## Testiranje

Pokretanje testova:
```bash
php artisan test
```

## Git Komiti

Projekat je organizovan u 10 smislenih komita:

1. **Inicijalizacija Laravel aplikacije** - osnovna struktura
2. **Kreiranje modela** - Soba, Poruka, ClanSobe
3. **Dodatne migracije** - indeksi, ograničenja, online status
4. **Implementacija kontrolera** - AuthController, SobaController, PorukaController
5. **API rute i Resource klase** - REST API konvencija
6. **WebSocket funkcionalnost** - NovaPoruka i KorisnikOnlineStatus eventi
7. **Migracije i seeders** - test podaci
8. **Middleware implementacija** - CheckSobaAccess i CheckAdminRole
9. **Testovi** - AuthControllerTest i SobaControllerTest
10. **Dokumentacija** - README i API dokumentacija

## Autori

- **Marko Stevanović** - glavni developer
- **Tim članovi** - kolaboratori

## Licenca

Ovaj projekat je kreiran za edukativne svrhe u okviru STEH kursa.
