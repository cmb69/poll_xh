# Poll_XH

Poll_XH ermöglicht die Platzierung von Umfragen auf Ihrer CMSimple_XH
Homepage. Sie können so viele Umfragen durchführen, wie Sie möchten, mit so
vielen Optionen wie Sie möchten (Einfach- oder Mehrfachauswahl). Die Wähler
werden durch Cookies und IP-Adressen unterschieden, so dass Schummeln
einigermaßen unwahrscheinlich ist.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Poll_XH ist ein Plugin für [CMSimple_XH](https://www.cmsimple-xh.org/de/).
Es erfordert CMSimple_XH ≥ 1.7.0, und PHP ≥ 7.1.0.
Poll_XH benötigt weiterhin das [Plib_XH](https://github.com/cmb69/plib_xh) Plugin;
ist dieses noch nicht installiert (see *Einstellungen*→*Info*),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

## Download

Das [aktuelle Release](https://github.com/cmb69/poll_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie das herunter geladene Archiv auf Ihrem Computer.
1. Laden Sie das komplette Verzeichnis `poll/` auf Ihren Server in
   das Pluginverzeichnis (`plugins/`) von CMSimple_XH.
1. Machen Sie die Unterverzeichnisse `css/` und `languages/` beschreibbar.
1. Browsen Sie zu `Plugins` → `Poll` im Administrationsbereich,
   um zu prüfen, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Konfiguration des Plugins erfolgt wie bei vielen anderen
CMSimple_XH-Plugins auch im Administrationsbereich der Homepage.
Gehen Sie zu `Plugins` → `Poll`.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können dort die
Sprachtexte in Ihre eigene Sprache übersetzen (falls keine entsprechende
Sprachdatei zur Verfügung steht), oder diese gemäß Ihren Wünschen anpassen.

Das Aussehen von Poll_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Sie können mit dem folgenden Pluginaufruf eine Umfrage auf einer
CMSimple_XH-Seite einbetten:

    {{{poll('name-der-umfrage')}}}

Um eine Umfrage im Template einzubinden, verwenden Sie:

    <?=poll('name-der-umfrage')?>

Statt `name-der-umfrage` können Sie einen beliebigen Namen, der nur aus lateinischen
Kleinbuchstaben (`a`-`z`), Ziffern (`0`-`9`) und Bindestrichen besteht, verwenden.
Sie können so viele Umfragen auf einer einzelnen Seite einbetten wie Sie möchten –
diese funktionieren unabhängig voneinander, solange sie verschiedene Namen haben.

Ist die Umfrage noch nicht beendet, und der Besucher hat noch nicht
abgestimmt, wird ihm die Abstimmungsansicht angezeigt, und er kann seine
Stimme abgeben. Nachdem bereits abgestimmt wurde, kann das Ergebnis der
Umfrage eingesehen werden.

Zurzeit müssen die Umfragedateien manuell erzeugt und bearbeitet werden.
Legen Sie einfach eine Datei `name-der-umfrage.csv` im
Unterordner `poll/` des `content/` Ordners von CMSimple_XH an.
Jede Stimmoption belegt eine eigene Zeile in der Datei.
Darüberhinaus gibt es zwei Metaoptionen, nämlich `%%%MAX%%%` und `%%%END%%%`,
die in separaten Zeilen aufgeführt werden müssen. Beide sind optional.

    %%%MAX%%%→3

erzeugt eine Umfrage mit Mehrfachauswahl, bei der der Benutzer höchstens 3
Optionen auswählen kann, und mit

    %%%END%%%→1335744000

kann das Enddatum der Umfrage als Unix-Zeitstempel angegeben werden.
Es ist zu beachten, dass das `→` für ein `TAB`- Zeichen steht.
Um den Unix-Zeitstempel eines Datum zu berechnen kann ein
[Online-Konverter](https://www.onlineconversion.com/unix_time.htm)
verwendet werden.

Als Beispiel wird `fifa-2026.csv` im `help/` Ordner ausgeliefert,
das das Dateiformat erklären sollte.
Das Ende dieser Umfrage wurde auf den Beginn des 2026 FIFA
World Cup festgelegt (nämlich dem 11. Juni 2026).
Nachdem diese Datei nach `content/poll/` verschoben wurde,
kann sie auf einer Seite wie folgt eingebettet werden:

    {{{poll('fifa-2026')}}}

## Einschränkungen

Das Zurücksetzen einer bereits gestarteten Umfrage ist nicht möglich, da
entsprechende Cookies bereits auf dem Rechner von Wählern gespeichert sein
können, so dass diese nicht erneut abstimmen könnten. Als Workaround müsste
die Umfrage umbenannt werden.

32bit PHP-Versionen können nicht mit Unix Zeitstempeln umgehen, die größer
als `2147483647` sind (was dem 19. Januar 2038 entspricht). Daher ist es mit
solchen Versionen nicht möglich, dass Umfragen nach diesem Datum enden.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/poll_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Poll_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Poll_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Poll_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

Tcheschische Übersetzung © Josef Němec.

## Danksagung

Das Pluginlogo wurde von [Jack Cai](https://www.doublejdesign.co.uk/) gestaltet.
Vielen Dank für die Veröffentlichung dieses Icons unter CC BY-ND.

Vielen Dank an die Gemeinschaft im [CMSimple_XH-Forum](https://www.cmsimpleforum.com/)
für Tipps, Anregungen und das Testen. Besonderer Dank gebührt *oldnema*,
*svasti*, *bca* und *Tata* für schnelles Feedback.

Und zu guter letzt vielen Dank an [Peter Harteg](https://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
