# E-wallet test project

Just a proof of work

## Installation

**Requirements:**
- PHP 7.2 (SHOULD work on 7.1 though)
- composer (https://getcomposer.org/download/)
- Postgresql 9.4 (10+ for native partman recomended)
- Bash to run install script.(It also used to build large files)

Make sure you have created a DB and you know USER/PASS for it.
Installing partman here is required too

```bash
git clone
cd /path/to/project/
chmod +x install.bash
chmod +x serve.bash
./install.bash
```
and follow instructions

**RUN**
---
For testing purposes builtin php webserver is used. 
```
./run.bash
```

**NOTE**

Please read carefully:
- Due to disabled AUTH token param must be set to userId.
- Amount must be sent with precision, refer coin table. *Eg to send 1 RUB amount must be set to 100*
- 

**API**
---

- Register client
```
POST http://localhost:8080/api/user/register
Accept: */*
Cache-Control: no-cache

{"wallet":"RUB","name":"Vasyae Pupkin", "city": "Somewhere", "country": "On Earth"}
```
- Transfer money between clients
```
POST http://localhost:8080/api/transfer/send
Accept: */*
Cache-Control: no-cache

{"token":"2","receiver":3, "amount":1000}
```
*amount must be set in sender's currency*

- Transfer between client by recepient's wallet
```
POST http://localhost:8080/api/transfer/send
Accept: */*
Cache-Control: no-cache

{"token":"2","receiver":3, "amount":1000,
"send_by_recevier_wallet": 1}

```

- Fill client's wallet
```
POST http://localhost:8080/api/transfer/fill
Accept: */*
Cache-Control: no-cache

{"receiver":2,"amount":100000}

```

- Update coin rate
```
POST http://localhost:8080/api/coin/update-rate
Accept: */*
Cache-Control: no-cache

{"code":"BTC","base":1, "quote":8700, "date":"2018-01-23"}
```
*"base":1, "quote":8700 means 1 BTC cost 8700 USD*
