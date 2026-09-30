<h1>New website contact enquiry</h1>
<dl>
    <dt>Name</dt><dd>{{ $details['name'] }}</dd>
    <dt>Email</dt><dd>{{ $details['email'] }}</dd>
    <dt>Phone</dt><dd>{{ $details['phone'] ?? 'Not provided' }}</dd>
    <dt>Interested in</dt><dd>{{ $details['interest'] }}</dd>
    <dt>Message</dt><dd>{!! nl2br(e($details['message'])) !!}</dd>
</dl>
