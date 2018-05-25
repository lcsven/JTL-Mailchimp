## MailChimp Anbindung für JTL-Shop4
(MailChimp API-Version 3.0)

---


### Pre-Requisiten

Bevor Sie beginnen, benötigen Sie folgende Dinge:

* ein **MailChimp-Konto**
* **eine Liste** in Ihrem MailChimp-Konto, welche Ihre Shop-Newsletterempfänger aufnimmt

Sie bekommen diese z.B. unter [MailChimp](https://login.mailchimp.com/signup).

Nachdem Sie sich Ihr Konto erstellt haben, brauchen Sie noch eine Liste,
zu der Ihr Shop Ihre Newsletterempfänger hinzufügen kann.

Haben Sie diese Voraussetzungen erfüllt, können Sie mit der Einrichtung Ihres
MailChimp3-Plugins, wie im Folgenden beschrieben, vorfahren.


### Tab "Einstellungen"

#### API-Key

In Ihrem MailChimp-Konto (hier auch Account genannt), finden Sie Ihren s.g. _API-key_
(unter `Account -> Extras -> API-key`).
Dieser _API-key_ ist erforderlich, damit MailChimp Ihren Shop identifizieren und autorisieren kann.

Tragen Sie diesen _API-key_ im MailChimp3-Plugin unter `Pluginverwaltung -> JTL-MailChimp3 -> Einstellungen` ein.

_Speichern Sie nun Ihre Einstellungen._

#### Empfänger-Liste

Konnte das Plugin erfolgreich eine Verbindung  mit Ihrem MailChimp-Konto aufnehmen, werden Ihnen
sodann alle gefundenen Listen Ihres Accounts angezeigt.

Wählen Sie nun Ihre gewünschte Liste, die Ihr Shop zum Abgleich benutzen soll, aus und
_klicken Sie nochmals auf Einstellungen Speichern_.

> Das "JTL-MailChimp3"-Plugin unterstützt derzeit die Kommunikation mit **einer** Liste.
>
> MailChimp empfiehlt zudem, möglichst auch nur eine einzige Empfängerliste zu pflegen
> und diese zudem möglichst "sauber" zu halten.
> Staffelungen von Mailings können Sie über Ihr MailChimp-Konto, mittels dort erstellbarer
> Kampagnen, Segmentierungen und anderer Unterteilungen vornehmen.
>
> Ziehen Sie hierzu bitte die MailChimp-Dokumentation zu Rate (siehe:
> [MailChimp Knowledge-Base](http://kb.mailchimp.com/campaigns/ways-to-build/create-a-campaign-with-campaign-builder)).

#### Automatischer Abgleich

Diese Einstellung ist standardmäßig auf "ON" gesetzt und bewirkt das automatische Eintragen,
bzw. das automatische Löschen von Newsletterempfängern, auf Ihrer MailChimp-Liste.



### Aktion und Buttons

#### Suchen

Das Suchfeld, im oberen Bereich Ihres Plugins, wird dazu benutzt, um gezielt nach e-Mail-Adressen von
Newsletterempfängern zu suchen.

Es reduziert die aktuell "betroffene" Menge Newsletterempfänger (siehe auch "Alle übertragen").

#### Gewählte übertragen

Dieser Button erlaubt es Ihnen, in Kombination mit den Checkboxes vor den Namen der Newsletterempfänger,
eine Auswahl dieser an Ihre MailChimp-Liste zu schicken.

Die Checkbox "Alle auswählen", am Fuße der jeweiligen Listen-Seite, bezieht sich hier immer nur
auf die aktuelle Seite.

#### Alle übertragen

Mit dieser Aktion werden sämtliche Newsletterempfänger, die im Shop registriert sind,
an MailChimp übertragen.

**ACHTUNG:** Dieser Vorgang kann - je nach Anzahl der Newsletterempfänger - einige Zeit in Anspruch nehmen.

**Bachten Sie:** Diese Funktion kann mit dem Suchfeld eingeschränkt werden.

Ist eine Suche aktiv, werden mit dem "Alle übertragen"-Button ausschließlich alle von der Suche erfassten
Datensätze an MailChimp übertragen.

Da MailChimp den Datensatz eines Newsletterempfängers anhand seiner e-Mail-Adresse identifiziert,
können bereits bekannte e-Mail-Adressen nicht noch einmal angelegt werden.

---

### Zusätzliches

MailChimp bietet dem Benutzer die Möglichkeit, wahlfrei Felder zu einem Newsletterempfängers-Datensatz
anzulegen. Standardmäßig sind dies `FNAME` für "first name" und `LNAME` "last name".

Eine Anrede ist hier leider nicht vorgesehen.

In Ihrem JTL-Shop wird diese Information allerdings bereits vorgehalten und
das "JTL-MailChimp3"-Plugin kann diese Information auch an MailChimp übertragen.

Das zusätzliche Feld, welches das Plugin überträgt, hat den Namen:
```
GENDER
```
und enthält die Werte `f` für "female und `m` für "male".

Um dieses Feld nun in Ihren MailChimp-Listen nutzen zu können, ist es erforderlich,
in Ihrem MailChimp-Konto ein neues "Merge"-Feld anzulegen.
(siehe: [MailChimp Merge-Tags](http://kb.mailchimp.com/merge-tags/getting-started-with-merge-tags))


---

### "Known Problems"

Da MailChimp eine weltweit intensiv genutzte Plattform ist, ist man dort gezwungen, starken Gebraucht
von Zwischenspeicherungs-Mechanismen ("caches") zu machen.
Dies führt dazu, dass beispielsweise die Listen-Anzeige im Benutzer-Konto nicht immer umgehend
die korrekten und tatsächlichen Anzahlen von Newsletterempfängern darstellt.

Es kann somit sein, dass ein soeben eingetragener Newsletterempfänger nicht sofort in der entsprechenden
MailChimp-Liste sichtbar ist.

Dies ist kein Fehler, sondern dem Umstand geschuldet, dass sich hier viele System miteinander abgleichen müssen.


