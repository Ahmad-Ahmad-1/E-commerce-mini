<!DOCTYPE html>
<html>

<head>
   <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="{{ asset('/admincss/vendor/bootstrap/css/bootstrap.min.css')}}">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="{{ asset('/ admincss/vendor/font-awesome/css/font-awesome.min.css')}}">
    <!-- Custom Font Icons CSS-->
    <link rel="stylesheet" href="{{asset('/admincss/css/font.css')}}">
    <!-- Google fonts - Muli-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="{{asset('/admincss/css/style.default.css')}}" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="{{asset('/admincss/css/custom.css')}}">
    <!-- Favicon-->
    <link rel="shortcut icon" href="{{asset('/admincss/img/favicon.ico')}}">
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
    <link rel="stylesheet" href="http://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>
    .div_deg{
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 60px;
    }
    .table_deg{
        border: 2px solid greenyellow;
    }
    th{
        background-color: skyblue;
        color: white;
        font-size: 19px;
        font-weight: bold;
        padding: 15px;
    }
    td{
      border: 1px solid skyblue;
      text-align: center;
    }
    h1{
      text-align: center;
      color: grey;
    }
    </style>

</head>
<body>
    <h1> Product Details </h1>
 <div class="div_deg">
            <table class="table_deg">
             <tr>
            <th> Product Title </th>
            <th> Description  </th>
            <th>Category </th>
            <th>Price </th>
            <th>Quantity </th>
            <th> Image </th>
            <th> Return </th>
            </tr>
            <tr>
            <td>{{$product->title}}</td>
            <td>{{$product->description}}</td>
            <td> {{$product->Category->category_name}}</td>
            <td style="color: red">{{$product->price}} $</td>
            <td>{{$product->quantity }}</td>
            <td>
               <img src="/images/{{$product->image}}" height="200" width="200">
             </td>
             <td>
              <a href="{{url('dashboard')}}" class="btn btn-success"> Return</a>
             </td>
            </tr>
        </table>
 </div>

</body>
</html>
