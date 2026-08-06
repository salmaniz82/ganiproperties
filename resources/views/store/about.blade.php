@extends('layouts.store')
@section('title', 'About Us | Gani Property Services')
@php($activePage = 'about')
@section('content')
<main id="top">
    <section class="page-banner about-banner" aria-labelledby="about-page-title">
        <div class="page-banner-shade"></div>
        <div class="page-banner-content">
            <nav class="breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span>About us</span>
            </nav>
            <p class="eyebrow eyebrow-light">INDEPENDENT. EXPERIENCED. LOCAL.</p>
            <h1 id="about-page-title">About Gani</h1>
            <p>Property expertise with a personal approach, right here in Balham.</p>
        </div>
    </section>

    <section class="about-intro section">
        <div class="about-intro-copy">
            <p class="eyebrow">YOUR LOCAL PROPERTY PARTNER</p>
            <h2>Independent advice, built around you</h2>
            <p>As an independent agent and a member of the National Association of Estate Agents, we aim to make every part of your property journey clear, accurate and straightforward. Our website is updated regularly so the information you need is useful and current.</p>
            <p>We cover sales, lettings, property management, land, new home development and buy-to-let across Balham and the surrounding areas.</p>
            <p>Because we are independent, our service is personal and flexible. You will receive honest guidance, focused marketing and a team that remains accountable from your first conversation through to completion or move-in.</p>
        </div>
        <aside class="about-promise">
            <span class="promise-mark">G</span>
            <p class="eyebrow">THE GANI PROMISE</p>
            <h2>Better value without compromising service</h2>
            <p>We are confident in the value we provide. We aim to beat any comparable agency fee while delivering a higher quality of service for you and your property.</p>
            <a href="{{ route('home') }}#valuation">Request a free valuation &#8594;</a>
        </aside>
    </section>

    <section class="about-services" id="services">
        <div class="section about-services-inner">
            <div class="about-section-heading">
                <div><p class="eyebrow">WHAT WE DO</p><h2>Property services under one roof</h2></div>
                <p>From a first appraisal to long-term management, our team brings local knowledge and practical support to every instruction.</p>
            </div>

            <div class="service-grid">
                <article class="service-card"><span class="service-number">01</span><svg><use href="#icon-key"/></svg><h3>Residential sales</h3><p>Accurate valuations, considered presentation, broad marketing and skilled negotiation designed to secure the right buyer and the best possible outcome.</p></article>
                <article class="service-card"><span class="service-number">02</span><svg><use href="#icon-chat"/></svg><h3>Lettings</h3><p>Support for landlords and tenants from marketing and viewings through referencing, agreements, deposits and a smooth move-in.</p></article>
                <article class="service-card"><span class="service-number">03</span><svg><use href="#icon-shield"/></svg><h3>Property management</h3><p>Responsive day-to-day management, rent collection, maintenance coordination and regular communication for greater peace of mind.</p></article>
                <article class="service-card"><span class="service-number">04</span><svg><use href="#icon-pin"/></svg><h3>Land &amp; development</h3><p>Local insight for landowners, developers and investors considering development opportunities, site potential and routes to market.</p></article>
                <article class="service-card"><span class="service-number">05</span><svg><use href="#icon-check"/></svg><h3>New homes</h3><p>Positioning and sales support for new-build homes, from launch strategy and buyer enquiries to reservations and completion.</p></article>
                <article class="service-card"><span class="service-number">06</span><svg><use href="#icon-heart"/></svg><h3>Buy-to-let</h3><p>Practical guidance for landlords and investors, informed by local demand, achievable rental values and long-term market potential.</p></article>
            </div>
        </div>
    </section>

    <section class="about-approach section">
        <div class="approach-image">
            <img src="/assets/about-service.jpg" alt="Model home and keys representing Gani property services" loading="lazy">
            <span>Sales &bull; Lettings &bull; Management</span>
        </div>
        <div class="approach-copy">
            <p class="eyebrow">HOW WE WORK</p>
            <h2>Clear communication at every step</h2>
            <p>Property decisions can be significant. Our role is to make the process easier to understand and easier to manage, with advice shaped by your priorities.</p>
            <ul class="approach-list">
                <li><svg><use href="#icon-check"/></svg><div><strong>A realistic starting point</strong><span>Thoughtful, evidence-led advice on value, timing and presentation.</span></div></li>
                <li><svg><use href="#icon-check"/></svg><div><strong>Marketing that earns attention</strong><span>Professional presentation and targeted exposure to serious buyers and tenants.</span></div></li>
                <li><svg><use href="#icon-check"/></svg><div><strong>One accountable local team</strong><span>Consistent updates and proactive progression from instruction to completion.</span></div></li>
            </ul>
        </div>
    </section>

    <section class="coverage" id="coverage">
        <div class="section coverage-inner">
            <div>
                <p class="eyebrow eyebrow-light">SOUTH LONDON KNOWLEDGE</p>
                <h2>At home in Balham and beyond</h2>
                <p>Our base on Balham High Road keeps us close to the people, streets and property market we serve every day.</p>
            </div>
            <ul aria-label="Areas covered">
                <li>Balham</li><li>Tooting</li><li>Streatham</li><li>Clapham</li><li>Wandsworth</li><li>South London</li>
            </ul>
        </div>
    </section>

    <section class="about-cta section">
        <p class="eyebrow">LET'S TALK PROPERTY</p>
        <h2>Thinking of selling, letting or investing?</h2>
        <p>Start with a friendly, no-obligation conversation with our Balham team.</p>
        <div>
            <a class="button" href="{{ route('home') }}#valuation">Book a free valuation</a>
            <a class="about-cta-phone" href="tel:02086737778"><svg class="icon"><use href="#icon-phone"/></svg>020 8673 7778</a>
        </div>
    </section>
</main>
@endsection
