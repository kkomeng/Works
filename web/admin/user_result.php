<!doctype html>
<html lang="ja">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- fontawesome -->
  <link href="./lib/fontawesome-free-5.15.1-web/css/all/css" rel="stylesheet">

  <!-- Original CSS -->
  <link rel="stylesheet" href="../css/style.css">

  <title>日報登録 | WoRKS</title>
</head>

<body class="text-center bg-success">

  <div>
    <img class="mb-4" src="./img/logo.svg" alt="WoRKS" width="80" height="80">
  </div>

  <form class="border rounded bg-white form-time-table" action="index.php">
    <h1 class="h3 my-3">月別リスト</h1>

    <select class="form-control rounded-pill mb-3" id="exampleFormControlSelect1">
      <option>2020 / 11</option>
    </select>

    <table class="table table-bordered">
      <thead>
        <tr class="bg-light">
          <th scope="col">日</th>
          <th scope="col">出勤</th>
          <th scope="col">退勤</th>
          <th scope="col">休憩</th>
          <th scope="col">業務内容</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1(水)</th>
          <td>09:00</td>
          <td>18:00</td>
          <td>01:00</td>
          <td>テストテストテストテストテスト...</td>
          <td><i class="fa-solid fa-pencil"></i></td>
        </tr>
      </tbody>
    </table>

  </form>

  <!-- Button trigger modal -->
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
    Launch demo modal
  </button>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <p></p>

          <h5 class="modal-title" id="exampleModalLabel">日報登録</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="alert alert-primary" role="alert">
              11/1(水)
            </div>
            <div class="row">
              <div class="col-sm">
                  <input type="text" class="form-control" placeholder="出勤">
              </div>
              <div class="col-sm">
                  <input type="text" class="form-control" placeholder="退勤">
              </div>
              <div class="col-sm">
                <input type="text" class="form-control" placeholder="休憩">
              </div>
            </div>
            <div class="form-group pt-3">
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="5" placeholder="業務内容"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary rounded-pill px-5">登録</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

  <!-- Option 2: jQuery, Popper.js, and Bootstrap JS
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    -->
</body>

</html>