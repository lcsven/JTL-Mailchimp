MailChimp Anbindung, API-Version 3.0, für JTL-Shop4
===================================================

... strongly under --DEVELOPMENT-- !


Tab "Einstellungen"
-------------------

Als Erstes ist es erforderlich, sich ein MailChimp-Konto zu erstellen.

In diesem MailChimp-Konto (im Folgenden auch -account genannt), finden Sie Ihren
s.g. API-key (unter "Account -> Extras -> API-key").
Dieser Schlüssel ist nötig, damit MailChimp Ihren shop identifizieren und autorisieren kann.

Nachdem Sie sich Ihr Konto erstellt haben, brauchen Sie noch eine Liste, zu
der Ihr shop Ihre Newsletterempfänger hinzufügen kann.

Das Plugin "JTL-MailChimp3" unterstützt derzeit die Kommunikation mit einer Liste.
MailChimp empfiehlt zudem, möglichst auch nur eine einzige Empfängerliste zu pflegen
und Diese möglichst "sauber" zu halten.
Staffelungen von Mailings können Sie über Ihr MailChimp-Konto mittels dort erstellbarer
Kampagnien, Segmentationen und anderer Unterteilungen vornehmen.
Ziehen Sie hierzu bitte die MailChimp-Dokumantation heran.


Haben Sie nun ein entsprechendes Konto und eine Liste in Ihrem MailChimp-account erstellt,
tragen Sie einfach den API-key im shop-backend ("Pluginverwaltung -> JTL-MailChimp3") ein
und das MailChimp-Plugin zeigt Ihnen sodann die gefundenen Listen an.
Nachdem Sie Ihre Liste im pulldown-Menü gewählt haben, speichern Sie bitte Ihre
Einstellungen.

Wählen Sie nun ggf. noch, ob sich shop-Nutzer, die sich ohne Ihr Zutun für Ihren
Newletter registrieren, automatisch an Ihre MailChimp-Liste gesendet werden sollen.
Die automatische Übertragung ist hier die Standardeinstellung.



Aktion "Alle übertragen"
------------------------

Hiermit werden sämtliche Newsletter-Empfänger, die im shop registriert sind,
an MailChimp übertragen.
Dieser Vorgang kann, je nach Menge, einige Zeit in Anspruch nehmen.

Jeder Newsletterempfänger representiert einen eigenen Datensatz, welcher zu MailChimp
übertragen wird.
Ein Datensatz wird hauptsächlich über die e-Mail-Adresse des Newsletterempfängers
identifiziert.

Die Aktion "Alle übertrage" aktualisiert bereits vorhandene Datensätze, die
ggf. bereits unter der gleichen e-Mail-Adresse in der antsprechenden MailChimp-Liste
vorhanden sind..


