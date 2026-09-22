
@extends('layout.master')

@section('title', 'Login Page')

@section('script')

    <script type="text/javascript">

        document.addEventListener('alpine:init', () => {

            Alpine.data('loginForm', () => ({

                cellphone: '',
                otp: '',
                loginToken: '',
                error: '',
                loading: false,
                loadingResend: false,

                checkOtpForm: false,

                // تایمر 1 دقیقه و 30 ثانیه
                seconds: 30,
                minutes: 1,

                async login() {

                    this.loading = true;
                    this.error = '';

                    const res = await fetch('{{ url('/login') }}', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },

                        body: JSON.stringify({
                            '_token': '{{ csrf_token() }}',
                            'cellphone': this.cellphone
                        })

                    });

                    const data = await res.json();

                    this.loading = false;

                    if (res.ok) {

                        this.loginToken = data.login_token;

                        this.checkOtpForm = true;

                        // شروع تایمر 1:30
                        this.seconds = 30;
                        this.minutes = 1;
                        this.timer();

                        this.error = '';

                    } else {

                        this.error = data.message;
                        console.log(data);

                    }

                },

                async checkOtp() {

                    this.loading = true;
                    this.error = '';

                    const res = await fetch('{{ url('/check-otp') }}', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },

                        body: JSON.stringify({
                            '_token': '{{ csrf_token() }}',
                            'otp': this.otp,
                            'login_token': this.loginToken
                        })

                    });

                    const data = await res.json();

                    this.loading = false;

                    console.log(data);

                    if (res.ok) {

                        this.error = '';

                        document.location.href = '{{ route('home.index') }}';

                    } else {

                        this.error = data.message;

                    }

                },

                async resendOtp() {

                    this.loadingResend = true;
                    this.error = '';

                    const res = await fetch('{{ url('/resend-otp') }}', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },

                        body: JSON.stringify({
                            '_token': '{{ csrf_token() }}',
                            'login_token': this.loginToken
                        })

                    });

                    const data = await res.json();

                    this.loadingResend = false;

                    console.log(data);

                    if (res.ok) {

                        // ذخیره login token جدید
                        this.loginToken = data.login_token;

                        // پاک کردن OTP قبلی
                        this.otp = '';

                        // شروع دوباره تایمر 1:30
                        this.seconds = 30;
                        this.minutes = 1;
                        this.timer();

                        this.error = '';

                    } else {

                        this.error = data.message;

                    }

                },

                timer() {

                    const interval = setInterval(() => {

                        if (this.seconds > 0) {

                            this.seconds = this.seconds - 1;

                        } else if (this.minutes > 0) {

                            this.minutes = this.minutes - 1;
                            this.seconds = 59;

                        } else {

                            clearInterval(interval);

                        }

                    }, 1000);

                },

            }))

        });

    </script>

@endsection

@section('content')

    <section class="auth_section book_section">

        <div class="container">

            <div class="row mt-5">

                <div class="col-md-4 offset-md-4">

                    <div x-data="loginForm" class="card">

                        <div class="card-body">

                            <div class="form_container">

                                {{-- cellphone input --}}

                                <template x-if="!checkOtpForm">

                                    <div>

                                        <div class="mb-3">

                                            <label class="form-label">
                                                شماره موبایل
                                            </label>

                                            <input
                                                type="text"
                                                x-model="cellphone"
                                                class="form-control mb-2"
                                                maxlength="11"
                                                inputmode="numeric"
                                                placeholder="مثلاً 09123456789"
                                            />

                                            <div
                                                class="form-text text-danger"
                                                x-text="error">
                                            </div>

                                        </div>

                                        <button
                                            @click="login()"
                                            type="button"
                                            class="btn btn-primary btn-auth"
                                            :disabled="loading"
                                        >
                                            ورود

                                            <div
                                                x-show="loading"
                                                class="spinner-border spinner-border-sm ms-2">
                                            </div>

                                        </button>

                                    </div>

                                </template>


                                {{-- otp input --}}

                                <template x-if="checkOtpForm">

                                    <div>

                                        <div class="mb-3">

                                            <label class="form-label">
                                                کد ورود
                                            </label>

                                            <input
                                                type="text"
                                                x-model="otp"
                                                class="form-control mb-2"
                                                maxlength="5"
                                                inputmode="numeric"
                                                placeholder="مثلاً 12345"
                                            />

                                            <div
                                                class="form-text text-danger"
                                                x-text="error">
                                            </div>

                                        </div>

                                        <div class="d-flex justify-content-between align-items-baseline">

                                            {{-- تایید کد --}}

                                            <button
                                                @click="checkOtp()"
                                                type="button"
                                                class="btn btn-primary btn-auth"
                                                :disabled="loading"
                                            >
                                                ارسال

                                                <div
                                                    x-show="loading"
                                                    class="spinner-border spinner-border-sm ms-2">
                                                </div>

                                            </button>


                                            {{-- تایمر --}}

                                            <template x-if="seconds > 0 || minutes > 0">

                                                <div
                                                    class="mb-1 me-3"
                                                    dir="ltr"
                                                    style="direction: ltr;"
                                                >

                                                    <span
                                                        x-text="minutes < 10 ? `0${minutes}` : minutes">
                                                    </span>

                                                    :

                                                    <span
                                                        x-text="seconds < 10 ? `0${seconds}` : seconds">
                                                    </span>

                                                </div>

                                            </template>


                                            {{-- ارسال دوباره --}}

                                            <template x-if="seconds === 0 && minutes === 0">

                                                <button
                                                    @click="resendOtp()"
                                                    type="button"
                                                    class="btn btn-dark"
                                                    :disabled="loadingResend"
                                                >
                                                    ارسال دوباره

                                                    <div
                                                        x-show="loadingResend"
                                                        class="spinner-border spinner-border-sm ms-2">
                                                    </div>

                                                </button>

                                            </template>

                                        </div>

                                    </div>

                                </template>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <style>

        input::placeholder {
            color: #adb5bd !important;
            opacity: 0.4 !important;
            font-weight: 300;
        }

    </style>

@endsection

