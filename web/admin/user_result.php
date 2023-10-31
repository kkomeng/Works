<?php
require_once(dirname(__FILE__) . '/../../config/config.php');
require_once(dirname(__FILE__) . '/../functions.php');

try {
  //1.ログイン状態をチェック
  session_start();

  if (!isset($_SESSION['USER']) || $_SESSION['USER']['auth_type'] != 1) {
    // if(isset($_SESSION['USER'])) {
    //ログインされていない場合はログイン画面へ
    header('Location: ./login.php');
    exit;
  }

  //対象ユーザーのIDをパラメータから取得
  $user_id = $_REQUEST['id'];

  if (!$user_id) {
    throw new Exception('ユーザーIDが不正', 500);
  }

  $pdo = connect_db();

  $err = array();

  $target_date = date('Y-m-d');

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //日報登録処理

    //入力値をPOSTパラメーターから取得
    $target_date = $_POST['target_date'];
    $modal_start_time = $_POST['modal_start_time'];
    $modal_end_time = $_POST['modal_end_time'];
    $modal_break_time = $_POST['modal_break_time'];
    $modal_comment = $_POST['modal_comment'];

    // 出勤時間の必須／形式チェック
    if (!$modal_start_time) {
      $err['modal_start_time'] = '出勤時間を入力してください。';
    } elseif (!preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/', $modal_start_time)) {
      $modal_start_time = '';
      $err['modal_start_time'] = '出勤時間を正しく入力してください。';
    }

    // 退勤時間の形式チェック
    if (!preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/', $modal_end_time)) {
      $modal_end_time = '';
      $err['modal_end_time'] = '退勤時間を正しく入力してください。';
    }

    // 休憩時間の形式チェック
    if (!preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/', $modal_break_time)) {
      $modal_break_time = '';
      $err['modal_break_time'] = '休憩時間を正しく入力してください。';
    }

    // 業務内容の最大サイズチェック
    if (mb_strlen($modal_comment, 'utf-8') > 2000) {
      $err['modal_comment'] = '業務内容が長すぎます。';
    }

    //バリデーションチェック
    if (empty($err)) {
      //対象日のデータがあるかどうかチェック
      $sql = "SELECT id FROM work WHERE user_id = :user_id AND date = :date LIMIT 1";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':user_id', (int) $user_id, PDO::PARAM_STR);
      $stmt->bindValue(':date', $target_date, PDO::PARAM_STR);
      $stmt->execute();
      $work = $stmt->fetch();

      if ($work) {
        //対象日があればUPDATE
        $sql = "UPDATE work SET start_time = :start_time, end_time = :end_time, break_time = :break_time, comment = :comment WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', (int) $work['id'], PDO::PARAM_INT);
        $stmt->bindValue(':start_time', $modal_start_time, PDO::PARAM_STR);
        $stmt->bindValue(':end_time', $modal_end_time, PDO::PARAM_STR);
        $stmt->bindValue(':break_time', $modal_break_time, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $modal_comment, PDO::PARAM_STR);
        $stmt->execute();
      } else {
        //対象日のデータが無ければINSERT
        $sql = "INSERT INTO work (user_id, date, start_time, end_time, break_time, comment) VALUES (:user_id, :date, :start_time, :end_time, :break_time, :comment)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', (int) $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':date', $target_date, PDO::PARAM_STR);
        $stmt->bindValue(':start_time', $modal_start_time, PDO::PARAM_STR);
        $stmt->bindValue(':end_time', $modal_end_time, PDO::PARAM_STR);
        $stmt->bindValue(':break_time', $modal_break_time, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $modal_comment, PDO::PARAM_STR);
        // //debug use: START
        // echo '$target_date: '."{$target_date}<br>";
        // exit();
        // //debug use: END
        $stmt->execute();
      }
    }
  } else {
    $modal_start_time = '';
    $modal_end_time = '';
    $modal_break_time = '01:00';
    $modal_comment = '';
  }

  //2.ユーザーの業務日報データを取得
  if (isset($_GET['m'])) {
    $yyyymm = $_GET['m'];
    $day_count = date('t', strtotime($yyyymm));

    if (count(explode('-', $yyyymm)) != 2) {
      throw new Exception('日付の指定が不正', 500);
    }

    // 今月～過去12ヶ月の範囲内かどうか
    $check_date = new DateTime($yyyymm.'-01');
    $start_date = new DateTime('first day of -11 month 00:00');
    $end_date = new DateTime('first day of this month 00:00');

    if ($check_date < $start_date || $end_date < $check_date) {
      throw new Exception('日付の範囲が不正', 500);
    }

  } else {
    $yyyymm = date('Y-m');
    $day_count = date('t');
  }

  $sql = "SELECT date, id, start_time, end_time, break_time, comment FROM work WHERE user_id = :user_id AND DATE_FORMAT(date, '%Y-%m') = :date";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', (int) $user_id, PDO::PARAM_INT);
  $stmt->bindValue(':date', $yyyymm, PDO::PARAM_STR);
  $stmt->execute();
  $work_list = $stmt->fetchAll(PDO::FETCH_UNIQUE);
} catch (Exception $e) {
  header('Location: ../error.php');
  exit;
}
?>
<!doctype html>
<html lang="ja">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
    integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

  <!-- fontawesome -->
  <!-- <link href="./lib/fontawesome-free-5.15.1-web/css/all/css" rel="stylesheet"> -->
  <script src="https://kit.fontawesome.com/729985a503.js" crossorigin="anonymous"></script>


  <!-- Original CSS -->
  <link rel="stylesheet" href="./css/style.css">

  <title>日報登録 | WoRKS</title>
</head>

<body class="text-center bg-secondary">

  <div>
    <img class="mb-4" src="./img/logo.svg" alt="WoRKS" width="80" height="80">
  </div>

  <form class="border rounded bg-white form-time-table" action="user_result.php">
    <h1 class="h3 my-3">月別リスト</h1>

    <div class="float-left">
      <select class="form-control rounded-pill mb-3" name="m" onchange="submit(this.form)">
        <option value="<?= date('Y-m') ?>">
          <?= date('Y/m') ?>
        </option>
        <?php for ($i = 1; $i < 12; $i++): ?>
          <?php $target_yyyymm = strtotime("- {$i}months"); ?>
          <option value="<?= date('Y-m', $target_yyyymm) ?>" <?php if ($yyyymm == date('Y-m', $target_yyyymm))
               echo 'selected' ?>>
            <?= date('Y/m', $target_yyyymm) ?>
          </option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="float-right">
          <a href="./user_list.php"><button type="button" class="btn btn-secondary rounded-pill px-5">社員一覧に戻る</button></a>
    </div>

    <table class="table table-success">
      <thead>
        <tr class="bg-light">
          <th scope="fix-col">日</th>
          <th scope="fix-col">出勤</th>
          <th scope="fix-col">退勤</th>
          <th scope="fix-col">休憩</th>
          <th>業務内容</th>
          <th scope="fix-col"></th>
        </tr>
      </thead>
      <tbody>
        <?php for ($i = 1; $i <= $day_count; $i++): ?>
          <?php
          $start_time = '';
          $end_time = '';
          $break_time = '';
          $comment = '';

          if (isset($work_list[date('Y-m-d', strtotime($yyyymm . '-' . $i))])) {
            $work = $work_list[date('Y-m-d', strtotime($yyyymm . '-' . $i))];

            if ($work['start_time']) {
              $start_time = date('H:i', strtotime($work['start_time']));
            }

            if ($work['end_time']) {
              $end_time = date('H:i', strtotime($work['end_time']));
            }

            if ($work['break_time']) {
              $break_time = date('H:i', strtotime($work['break_time']));
            }

            if ($work['comment']) {
              $comment = mb_strimwidth($work['comment'], 0, 40, '...');
            }
          }
          ?>
          <tr>
            <th scope="row">
              <?= time_format_dw($yyyymm . '-' . $i) ?>
            </th>
            <td>
              <?= $start_time ?>
            </td>
            <td>
              <?= $end_time ?>
            </td>
            <td>
              <?= $break_time ?>
            </td>
            <td>
              <?= $comment ?>
            </td>
            <td><button type="button" class="btn btn-default h-auto ph-0" data-toggle="modal" data-target="#inputModal"
                data-day="<?= $yyyymm . '-' . sprintf('%02d', $i) ?>"><i class="fas fa-pencil-alt"></i></button></td>
          </tr>
        <?php endfor; ?>
      </tbody>
    </table>
  </form>

  <!-- Modal -->
  <form method="POST">
    <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <?= date('n', strtotime($target_date)) ?>/<span id="modal_day">
                  <?= time_format_dw($target_date) ?>
                </span>
              </div>
              <div class="row">
                <div class="col-sm">
                  <div class="input-group">
                    <input type="text"
                      class="form-control<?php if (isset($err['modal_start_time']))
                        echo ' is-invalid'; ?>"
                      placeholder="出勤" id="modal_start_time" name="modal_start_time"
                      value="<?= format_time($modal_start_time) ?>" required>
                    <div class="input-group-prepend">
                      <button type="button" class="input-group-text" id="start_btn">打刻</button>
                    </div>
                    <div class="invalid-feedback">
                      <?= $err['modal_start_time'] ?>
                    </div>
                  </div>
                </div>
                <div class="col-sm">
                  <div class="input-group">
                    <input type="text"
                      class="form-control<?php if (isset($err['modal_end_time']))
                        echo ' is-invalid'; ?>"
                      placeholder="退勤" id="modal_end_time" name="modal_end_time"
                      value="<?= format_time($modal_end_time) ?>">
                    <div class="input-group-prepend">
                      <button type="button" class="input-group-text" id="end_btn">打刻</button>
                    </div>
                    <div class="invalid-feedback">
                      <?= $err['modal_end_time'] ?>
                    </div>
                  </div>
                </div>
                <div class="col-sm">
                  <input type="text"
                    class="form-control<?php if (isset($err['modal_break_time']))
                      echo ' is-invalid'; ?>"
                    placeholder="休憩" id="modal_break_time" name="modal_break_time"
                    value="<?= format_time($modal_break_time) ?>">
                  <div class="invalid-feedback">
                    <?= $err['modal_break_time'] ?>
                  </div>
                </div>
              </div>
              <div class="form-group pt-3">
                <textarea class="form-control<?php if (isset($err['modal_comment']))
                  echo ' is-invalid'; ?>"
                  id="modal_comment" name="modal_comment" rows="5" placeholder="業務内容"><?= $modal_comment ?></textarea>
                <div class="invalid-feedback">
                  <?= $err['modal_comment'] ?>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary rounded-pill px-5">登録</button>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" id="target_date" name="target_date">
  </form>

  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
    crossorigin="anonymous"></script>

  <script>
    <?php if (!empty($err)): ?>
      var inputModal = new bootstrap.Modal(document.getElementById('inputModal'))
      inputModal.toggle()
    <?php endif; ?>

    $('#start_btn').click(function () {
      const now = new Date()
      const hour = now.getHours().toString().padStart(2, '0')
      const minute = now.getMinutes().toString().padStart(2, '0')
      $('#modal_start_time').val(hour + ':' + minute)
    })

    $('#end_btn').click(function () {
      const now = new Date()
      const hour = now.getHours().toString().padStart(2, '0')
      const minute = now.getMinutes().toString().padStart(2, '0')
      $('#modal_end_time').val(hour + ':' + minute)
    })

    $('#inputModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget)
      var target_day = button.data('day')
      // console.log(target_day)

      /*編集ボタンが押された対象日の表データを取得*/
      var day = button.closest('tr').children('th')[0].innerText
      var start_time = button.closest('tr').children('td')[0].innerText
      var end_time = button.closest('tr').children('td')[1].innerText
      var break_time = button.closest('tr').children('td')[2].innerText
      var comment = button.closest('tr').children('td')[3].innerText

      /*取得したデータをモーダルの各欄に設定*/
      $('#modal_day').text(day)
      $('#modal_start_time').val(start_time)
      $('#modal_end_time').val(end_time)
      $('#modal_break_time').val(break_time)
      $('#modal_comment').val(comment)
      $('#target_date').val(target_day)

      /*エラー表示をクリア*/
      $('#modal_start_time').removeClass('is-invalid')
      $('#modal_end_time').removeClass('is-invalid')
      $('#modal_break_time').removeClass('is-invalid')
      $('#modal_comment').removeClass('is-invalid')
    })
  </script>

  <!-- Option 2: jQuery, Popper.js, and Bootstrap JS
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    -->
</body>

</html>