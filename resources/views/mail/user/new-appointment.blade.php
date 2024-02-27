<x-mail::message>
# Ein neuer Telefontermin wurde für Sie eingetragen.

<p>Datum: {{ parseDate($appointment->start_date, 'M d, Y') }}</p>
<p>Uhrzeit: {{ parseDate($appointment->start_date, 'h:i A') }}</p>
<p>Kontakt: {{ $appointment->contact }}</p>


</x-mail::message>
