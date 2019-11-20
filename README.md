# SMNBOTS VERSION 1.0 - SINGLE

Hast du Angst über die Sicherheit deiner Daten bei öffentlichen Hostern? Nerven dich die ganzen Einschränkungen? Willst du Herr deiner eigenen Bot Armee sein? Also warum nicht einfach selber hosten!

> Das ehemalige SMNBOTS Team hat sich dazu entschieden jedem die Möglichkeit zu geben, selber seine Bots zu hosten und das auf einem gewohnten Interface. Was du hier bekommst, ist keine geklaute oder billig nachprogrammierte Version von SMNBOTS nein. Hier bekommst du die originale Version des SMNBOTS V1 Interfaces in der Single variante.

# Installation

- Installiere den [`TS3 Audiobot`](https://github.com/Splamy/TS3AudioBot#install) von Splamy
- Lade alle Dateien aus dem `web` Ordner auf deinen Webserver hoch
- Importiere die SQL Datei in deine Datenbank
- Trage alle benötigten Werte in der Config ein `/vendor/smnjan/Config.php`
```php
const  
  DB_HOST = "",  // Die Adresse deines Datenbankservers   
  DB_NAME = "",  // Der Name deiner Datenbank
  DB_USER = "",  // Benutzername deines Datenbank Nutzers
  DB_PSSWD = ""; // Passwort deines Datenbank Nutzers
  
  
const nodes = array(  
  1 => array(  
  'host' => '',   // Adresse deines TS3AudioBot Servers
  'port' => 1234, // Port deines TS3AudioBot Servers
  'key' => '',    // API Token (Dafür musst du den Bot mit !api token anschreiben)
  'name' => ''    // Der Interne Anzeigename
  )
);
```
**Und schon bist du bereit deine eigenen Musicbots zu steuern**
- Melde dich einfach mit dem Passwort **smnjan** an. Das kannst du später auch in deinem Profil ändern

# FAQ
- **Wie füge ich eine weitere Node hinzu?**
> Eine weitere Node fügst ohne große Kopfschmerzen ein. Gehe dazu einfach in die Config `/vendor/smnjan/Config.php` scrolle bis nach unten zum Bereich `const nodes = array(...)`. Dort stehen alle deine Nodes. Um nun eine weitere Node hinzuzufügen, machst du einfach nach dem letzten Eintrag ein ``,`` und fügst den unten stehenden Code ein (natürlich mit deinen Werten)
```php
2 => array(       // Die 2 ist immer die Letzte nummer der Liste Plus 1 gerechntet (also nach dem EIntrag würde z.B. eine 3 kommen) 
  'host' => '',   // Adresse deines TS3AudioBot Servers
  'port' => 1234, // Port deines TS3AudioBot Servers
  'key' => '',    // API Token (Dafür musst du den Bot mit !api token anschreiben)
  'name' => ''    // Der Interne Anzeigename
  )
```

- **Bei wem darf ich mich bedanken ?**
> Für die Entwicklung bei [Kuhva](https://twitter.com/KuhvaDE) und für das Projekt bei [Simon](https://twitter.com/SMNDMDE)


License
----
lgpl-3.0

**Wenn du etwas veränderst, denke bitte daran, das Links, Logos oder Texte die auf `SMNJAN`,`SMNBOTS` oder ``KUHVA`` zeigen nicht verändert werden dürfen.**
> **Ausnahmen sind die Twitter Links im Footer, das Logo im Interface so wie das Profilbild**

Wir machen das ganze hier gratis für dich also würde es uns sehr freuen wenn du diese kleine Limitierung einhalten könntest.
