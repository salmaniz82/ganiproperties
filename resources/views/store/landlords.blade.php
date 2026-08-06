@extends('layouts.store')
@section('title', 'Landlord & Property Management Services | Gani Property Services')
@php($activePage = 'landlords')
@section('content')
<main id="top">
    <section class="page-banner landlord-banner" aria-labelledby="landlord-page-title">
        <div class="page-banner-shade"></div>
        <div class="page-banner-content">
            <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span>Landlords</span></nav>
            <p class="eyebrow eyebrow-light">LETTINGS &amp; MANAGEMENT</p>
            <h1 id="landlord-page-title">Property management, handled.</h1>
            <p>Practical support for your property, your tenants and your long-term investment.</p>
        </div>
    </section>

    <section class="landlord-intro section">
        <div class="landlord-intro-image"><img src="/assets/office-ref.jpg" alt="Gani Property Services office in Balham" loading="lazy"><span>Independent property management from Balham</span></div>
        <div class="landlord-intro-copy">
            <p class="eyebrow">LESS ADMIN. MORE CONFIDENCE.</p>
            <h2>A hands-on team for hands-off ownership</h2>
            <p>Managing a rental property involves far more than collecting rent. Marketing, tenant communication, maintenance, documentation and renewals all require time and consistent attention.</p>
            <p>Our fully managed service brings those responsibilities together through one accountable local team. We look after the day-to-day details, keep you informed and help protect the experience of both landlord and tenant.</p>
            <div class="landlord-benefits"><div><strong>One local team</strong><span>A consistent point of contact throughout the tenancy.</span></div><div><strong>Clear reporting</strong><span>Useful updates without unnecessary administration.</span></div><div><strong>Proactive care</strong><span>Issues coordinated early to reduce disruption and cost.</span></div></div>
        </div>
    </section>

    <section class="landlord-services" id="management-services">
        <div class="section">
            <div class="about-section-heading"><div><p class="eyebrow">FULLY MANAGED LETTINGS</p><h2>Management from move-in to renewal</h2></div><p>Our service is designed to reduce day-to-day demands while keeping you connected to important decisions about your property.</p></div>
            <div class="service-grid landlord-service-grid">
                <article class="service-card"><span class="service-number">01</span><svg><use href="#icon-pin"/></svg><h3>Valuation &amp; marketing</h3><p>Rental advice, considered presentation, enquiries and accompanied viewings aimed at finding the right tenant.</p></article>
                <article class="service-card"><span class="service-number">02</span><svg><use href="#icon-check"/></svg><h3>Tenant onboarding</h3><p>Referencing coordination, agreements, deposit administration, inventory arrangements and a clear move-in process.</p></article>
                <article class="service-card"><span class="service-number">03</span><svg><use href="#icon-key"/></svg><h3>Rent collection</h3><p>Scheduled rent collection, statements and prompt follow-up if a payment does not arrive as expected.</p></article>
                <article class="service-card"><span class="service-number">04</span><svg><use href="#icon-shield"/></svg><h3>Maintenance coordination</h3><p>A responsive point of contact for tenants, with repairs assessed and suitable contractors coordinated with your approval.</p></article>
                <article class="service-card"><span class="service-number">05</span><svg><use href="#icon-chat"/></svg><h3>Inspections &amp; updates</h3><p>Periodic visits and practical reporting to help identify upkeep requirements and support good tenant relationships.</p></article>
                <article class="service-card"><span class="service-number">06</span><svg><use href="#icon-heart"/></svg><h3>Renewals &amp; check-out</h3><p>Rent review guidance, renewal conversations and organised check-out support when a tenancy comes to an end.</p></article>
            </div>
        </div>
    </section>

    <section class="landlord-management">
        <div class="section landlord-management-inner">
            <div>
                <p class="eyebrow eyebrow-light">PROTECTING THE BIGGER PICTURE</p>
                <h2>Supporting reliable income and long-term value</h2>
                <p>Good management helps reduce avoidable voids, keeps maintenance organised and creates a better experience for reliable tenants. We balance everyday decisions with your longer-term plans for the property.</p>
                <a class="button button-white" href="{{ route('contact', ['interest' => 'Property management']) }}">Discuss property management</a>
            </div>
            <ul>
                <li><svg><use href="#icon-check"/></svg><div><strong>Responsive tenant communication</strong><span>Questions and maintenance reports handled through a clear point of contact.</span></div></li>
                <li><svg><use href="#icon-check"/></svg><div><strong>Informed landlord decisions</strong><span>Options, costs and material issues explained before action is taken.</span></div></li>
                <li><svg><use href="#icon-check"/></svg><div><strong>Organised tenancy records</strong><span>Key documents, statements and communications kept together throughout the tenancy.</span></div></li>
            </ul>
        </div>
    </section>

    <section class="landlord-process section">
        <div class="landlord-process-heading"><p class="eyebrow">HOW IT WORKS</p><h2>A straightforward route to a managed tenancy</h2><p>Clear stages, regular communication and one local team from appraisal onward.</p></div>
        <ol>
            <li><span>01</span><div><h3>Rental appraisal</h3><p>We discuss your property, priorities, likely rent and the level of service you need.</p></div></li>
            <li><span>02</span><div><h3>Prepare &amp; market</h3><p>Presentation, marketing, viewings and applicant communication are coordinated.</p></div></li>
            <li><span>03</span><div><h3>Agree the tenancy</h3><p>Referencing, documentation, deposit arrangements and move-in are brought together.</p></div></li>
            <li><span>04</span><div><h3>Manage day to day</h3><p>We handle rent, tenant queries, maintenance coordination and scheduled updates.</p></div></li>
            <li><span>05</span><div><h3>Review &amp; renew</h3><p>We discuss rent, renewal options and next steps ahead of key tenancy dates.</p></div></li>
        </ol>
        <p class="landlord-note">Landlords remain responsible for meeting their legal obligations. Our team can help coordinate the practical steps and documentation associated with a managed tenancy.</p>
    </section>

    <section class="landlord-cta"><div><p class="eyebrow eyebrow-light">YOUR PROPERTY. PROFESSIONALLY MANAGED.</p><h2>Ready for a more hands-off approach?</h2><p>Book a no-obligation rental appraisal and talk through the right management service for your investment.</p></div><a class="button button-white" href="{{ route('contact', ['interest' => 'Property management']) }}">Book a landlord consultation</a><a class="phone" href="tel:02086737778"><svg class="icon"><use href="#icon-phone"/></svg>020 8673 7778</a></section>
</main>
@endsection
