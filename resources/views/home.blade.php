@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in!

                    <form id="form" action="{{ route('uploadZip') }}" method="post" enctype="multipart/form-data">
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <input id="uploadImage" type="file" name="image" />
                        <input class="btn btn-success" type="submit" value="Upload">
                    </form>
                            <div id="err"></div>
{{--                    <div class="uploadZipForm">--}}
{{--                        Zip archive:--}}
{{--                        <br />--}}
{{--                        <input id="zipArchive" type="file" name="zip" />--}}
{{--                        <br /><br />--}}
{{--                        <button id ="uploadZip">Save</button>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#form").on('submit',(function(e) {
                e.preventDefault();
                let file = new FormData(this);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('uploadZip') }}",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    beforeSend : function()
                    {
                        //$("#preview").fadeOut();
                        $("#err").fadeOut();
                    },
                    success: function(data)
                    {
                        if(data === 'invalid')
                        {
                            // invalid file format.
                            $("#err").html("Invalid File !").fadeIn();
                        }
                        else
                        {
                           $("#err").html(data).fadeIn();
                        }
                    },
                    error: function(e)
                    {
                        $("#err").html(e).fadeIn();
                    }
                });
            }));










            {{--$('#uploadZip').on('click', function () {--}}
            {{--    let file = $('#zipArchive').attr('file');--}}
            {{--    let form = new FormData();--}}
            {{--    form.append('file', file);--}}
            {{--    $.ajax({--}}
            {{--        url: "{{ route('uploadZip') }}",--}}
            {{--        dataType: 'json',--}}
            {{--        cache: false,--}}
            {{--        contentType: false,--}}
            {{--        processData: false,--}}
            {{--        data: {--}}
            {{--            "data": form,--}}
            {{--            "_token": "{{ csrf_token() }}",--}}
            {{--        },--}}
            {{--        method: 'POST',--}}
            {{--        success: function (message) {--}}
            {{--            alert(message);--}}
            {{--        }--}}
            {{--    });--}}
            {{--});--}}
        });
    </script>

@endsection