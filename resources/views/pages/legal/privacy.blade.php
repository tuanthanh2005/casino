@extends('layouts.app')

@section('title', 'Privacy Policy - Aquahub.pro')

@section('content')
<div class="section" style="background: white; padding-bottom: 2rem;">
    <div class="container" style="max-width: 800px;">
        <h1 style="font-size: 3rem; margin-bottom: 1.5rem;">Privacy Policy</h1>
        <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 3rem;">Last updated: {{ date('M d, Y') }}</p>
        
        <div class="prose" style="line-height: 1.8; color: #334155;">
            <p>At Aquahub.pro, accessible from {{ url('/') }}, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Aquahub.pro and how we use it.</p>
            
            <h2 style="margin-top: 3rem;">Log Files</h2>
            <p>Aquahub.pro follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks.</p>
            
            <h2 style="margin-top: 3rem;">Cookies and Web Beacons</h2>
            <p>Like any other website, Aquahub.pro uses 'cookies'. These cookies are used to store information including visitors' preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users' experience by customizing our web page content based on visitors' browser type and/or other information.</p>
            
            <h2 style="margin-top: 3rem;">Google DoubleClick DART Cookie</h2>
            <p>Google is one of a third-party vendor on our site. It also uses cookies, known as DART cookies, to serve ads to our site visitors based upon their visit to www.website.com and other sites on the internet.</p>
            
            <h2 style="margin-top: 3rem;">Our Advertising Partners</h2>
            <p>Some of advertisers on our site may use cookies and web beacons. Our advertising partners include:</p>
            <ul>
                <li>Google AdSense</li>
                <li>Amazon Associates</li>
            </ul>
        </div>
    </div>
</div>
@endsection
