<section class="landlord-enquiry section" id="landlord-enquiry" aria-labelledby="landlord-enquiry-title">
    <div class="landlord-enquiry-intro">
        <p class="eyebrow">LANDLORD ENQUIRY</p>
        <h2 id="landlord-enquiry-title">Tell us about your property</h2>
        <p>Share a few details and our team will contact you about the right service for your property.</p>
    </div>
    <div class="contact-form-panel">
        @if(session('landlord_enquiry_sent'))
            <p class="landlord-enquiry-feedback" role="status">{{ session('landlord_enquiry_sent') }}</p>
        @endif
        @if($errors->has('landlord_enquiry'))
            <p class="landlord-enquiry-feedback landlord-enquiry-feedback--error" role="alert">{{ $errors->first('landlord_enquiry') }}</p>
        @endif
        <form class="contact-form" action="{{ route('landlords.enquiry') }}" method="post">
            @csrf
            <div class="form-row">
                <label class="form-field @error('name') has-error @enderror"><span>Name</span><input type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="100" required>@error('name')<small>{{ $message }}</small>@enderror</label>
                <label class="form-field @error('email') has-error @enderror"><span>Email</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="254" required>@error('email')<small>{{ $message }}</small>@enderror</label>
            </div>
            <div class="form-row">
                <label class="form-field @error('phone') has-error @enderror"><span>Phone</span><input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" maxlength="30" required>@error('phone')<small>{{ $message }}</small>@enderror</label>
                <label class="form-field @error('property_type') has-error @enderror"><span>Property type</span><select name="property_type" required><option value="">Select property type</option>@foreach(['Flat / apartment', 'House', 'Commercial property', 'Other'] as $type)<option value="{{ $type }}" @selected(old('property_type') === $type)>{{ $type }}</option>@endforeach</select>@error('property_type')<small>{{ $message }}</small>@enderror</label>
            </div>
            <label class="form-field @error('address') has-error @enderror"><span>Property address</span><textarea name="address" rows="3" maxlength="500" required>{{ old('address') }}</textarea>@error('address')<small>{{ $message }}</small>@enderror</label>
            <p class="form-privacy">We will use these details only to respond to your property enquiry.</p>
            <button class="button" type="submit">Send property enquiry</button>
        </form>
    </div>
</section>
