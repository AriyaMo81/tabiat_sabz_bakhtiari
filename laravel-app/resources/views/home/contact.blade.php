    <!-- contact section -->
    <section id="contact" class="book_section layout_padding">
        <div class="container">
            <div class="heading_container">
                <h2>
                    تماس با ما
                </h2>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form_container">
                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div>
                                <input name="name" type="text" class="form-control" placeholder="نام و نام خانوادگی" value="{{ old('name') }}" />
                                <div class="form-text text-danger">@error('name') {{ $message }} @enderror</div>
                            </div>
                            <div>
                                <input name="email" type="email" class="form-control" placeholder="ایمیل" value="{{ old('email') }}" />
                                <div class="form-text text-danger">@error('email') {{ $message }} @enderror</div>
                            </div>
                            <div>
                                <input name="subject" type="text" class="form-control" placeholder="موضوع پیام" value="{{ old('subject') }}" />
                                <div class="form-text text-danger">@error('subject') {{ $message }} @enderror</div>
                            </div>
                            <div>
                                <textarea name="body" rows="10" style="height: 100px" class="form-control" placeholder="متن پیام">{{ old('body') }}</textarea>
                                <div class="form-text text-danger">@error('body') {{ $message }} @enderror</div>
                            </div>
                            <div class="btn_box">
                                <button type="submit">
                                    ارسال پیام
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="map_container ">
                        <div id="map" style="height: 345px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end contact section -->