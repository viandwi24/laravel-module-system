<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Management Module</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
    @include('ModuleSystem::navbar')

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-sm-12">
                @if ($errors->any())
                    @foreach ($errors->all() as $item)
                        <div class="alert alert-danger">
                            <b>Error !</b>
                            {{ $item }}
                        </div>                        
                    @endforeach                    
                @endif
                <div class="card">
                    <div class="card-header">List Module</div>
                    <div class="card-body">
                        <div class="text-right mb-4">
                            <form method="POST" action="{{ route('module.install') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="zip" style="display: none;">
                                <button id="btn-install" class="btn btn-success">Install .zip</button>
                            </form>
                        </div>
                        @include('ModuleSystem::component')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script>
        $(document).ready(()=>{
            $('#btn-install').on('click', (e) => {
                e.preventDefault();
                $('input[name=zip]').click();
            });
            $('input[name=zip]').change(function(evt) {
                $('form').submit();
            });
        });
    </script>
</body>
</html>