# sanbot

Un bot Telegram per pubblicare i santi del giorno, per invocare sempre il santo giusto quando qualcosa non funziona.

## SANBOT.INI
Tutte le informazioni private sono registrate nel file `sanbot.ini` che ha questo formato:

```
[Telegram]
APIurl=https://api.telegram.org/bot<chiave del bot>
ChatID=<id della chat>

[Santi]
JSONurl=https://www.santodelgiorno.it/santi.json
```

Per fare in modo che questo file non venga prelevato via http aggiungere questa riga nella configurazione del virtual host Apache:

`RedirectMatch 404 ^/setup.ini`
BAU
