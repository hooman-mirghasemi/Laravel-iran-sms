<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sms list</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoWe/6oMVemdAVTMs2xqW4mwXrXsW0L84Iytr2wi5v2QjrP/xp" crossorigin="anonymous"></script>

    <style>
        body {
        font-size: x-small;
        }
    </style>

</head>
<body>
<div class="container">
    <main>
        <table class="table table-striped" style="margin-top: 20px">
            <thead>
            <tr>
                <th scope="col">id</th>
                <th scope="col">mobile</th>
                <th scope="col">message</th>
                <th scope="col">from</th>
                <th scope="col">number</th>
                <th scope="col">web_service_response</th>
                <th scope="col">success</th>
                <th scope="col">created_at</th>
                <th scope="col">updated_at</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($data))
                @foreach($data as $sms)
                    <tr>
                        <td>{{$sms['id']}}</td>
                        <td>{{$sms['mobile']}}</td>
                        <td>{{$sms['message']}}</td>
                        <td>{{$sms['from']}}</td>
                        <td>{{$sms['number']}}</td>
                        <td>{{$sms['web_service_response']}}</td>
                        <td>{{$sms['success']}}</td>
                        <td>{{$sms['created_at']}}</td>
                        <td>{{$sms['updated_at']}}</td>
                    </tr>
                @endforeach
            @endif
            </tbody>

        </table>
        <div class="d-flex justify-content-center">
            {!! $data->links() !!}
        </div>
    </main>
</div>
</body>
</html>
