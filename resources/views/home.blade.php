@extends('layouts.app')



@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                {{-- <center>
                    <button id="btn-nft-enable" onclick="initFirebaseMessagingRegistration()"
                        class="btn btn-danger btn-xs btn-flat">Allow for Notification</button>
                </center> --}}
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}

                            </div>
                        @endif
                        <form action="{{ route('send.notification') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title">
                            </div>
                            <div class="form-group">
                                <label>Body</label>
                                <textarea class="form-control" name="body"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Notification</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js"></script>
    <script>
        $(document).ready(function() {
            initFirebaseMessagingRegistration();
        });

        const firebaseConfig = {
            apiKey: "AIzaSyAZCa6o-DPX4NWxjZJlBIzKnjrlDz3l7YM",
            authDomain: "flettonchatbot.firebaseapp.com",
            projectId: "flettonchatbot",
            storageBucket: "flettonchatbot.appspot.com",
            messagingSenderId: "691698478961",
            appId: "1:691698478961:web:293a80ed82b837e32210d0",
            measurementId: "G-E6G35MR752"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        const VAPID_PUBLIC_KEY = "BEIGavZ1af_gFgl-yRjgvgg-d6ID8rThq__zgFCJ4pVzLGXw4v6bQdVZGIvm9frMVqIabhzmBDiZ9CDy4u85La8";

        async function initFirebaseMessagingRegistration() {
            const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
            const isInStandaloneMode = ('standalone' in navigator) && navigator.standalone === true;

            if (isIOS && !isInStandaloneMode) {
                console.log("iOS Safari tab detected — must be installed as PWA to enable notifications.");
                return;
            }

            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    console.warn('Notifications permission was not granted.');
                    return;
                }

                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

                const token = await messaging.getToken({
                    vapidKey: VAPID_PUBLIC_KEY,
                    serviceWorkerRegistration: registration
                });

                if (token) {
                    console.log('FCM Token:', token);

                    $.post('{{ route('save-token') }}', {
                            _token: '{{ csrf_token() }}',
                            token
                        })
                        .done(() => console.log('Token saved successfully.'))
                        .fail(e => console.error('Save token error:', e));
                } else {
                    console.warn('No FCM token received.');
                }
            } catch (e) {
                console.error('Error in FCM registration:', e);
            }
        }

        messaging.onMessage((payload) => {
            console.log('onMessage:', payload);

            const d = payload.data || payload.notification || {};
            const title = d.title || 'Notification';
            const options = {
                body: d.body || '',
                icon: d.icon || '/favicon.ico'
            };

            new Notification(title, options);
        });
    </script>
@endsection
