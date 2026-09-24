<h1>New landlord service enquiry</h1>
<p>A property owner has requested information about Gani Property Services.</p>
<dl>
    <dt>Name</dt><dd>{{ $details['name'] }}</dd>
    <dt>Email</dt><dd>{{ $details['email'] }}</dd>
    <dt>Phone</dt><dd>{{ $details['phone'] }}</dd>
    <dt>Property address</dt><dd>{!! nl2br(e($details['address'])) !!}</dd>
    <dt>Property type</dt><dd>{{ $details['property_type'] }}</dd>
</dl>
