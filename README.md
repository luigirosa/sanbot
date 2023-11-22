# sanbot

Bot da orbi!
Un bot per santificare i canali Telegram.

## SANBOT.INI
Tutte le informazioni private sono registrate nel file `sanbot.ini` che ha questo formato:

```
[Telegram]
APIurl=https://api.telegram.org/bot<chiave del bot>
ChatID=<id della chat>

[Santi]
JSONurl=https://www.santodelgiorno.it/santi.json
```

ChatID si ricava dalla risposta JSON di Telegram e serve al programma per rispondere direttamente ad una richiesta.
