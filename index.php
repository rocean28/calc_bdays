<?
$style_filemtime = '';
$function_filemtime = '';
if($_SERVER['DOCUMENT_ROOT'] != '/var/www/html') {
  $style_filemtime = filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css');
  $function_filemtime = filemtime($_SERVER['DOCUMENT_ROOT'] . '/js/function.js');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>【営業日計算】制作スケジュール確認ツール</title>
<link href="https://use.fontawesome.com/releases/v6.2.0/css/all.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="./css/style.css?<?= $style_filemtime ?>">
</head>
<body>
<header class="header">
【営業日計算】制作スケジュール確認ツール
</header>
<div id="app" class="wrap mt-5" v-cloak>
  <div class="main">
    <!-- <div class="btn-changeAll-wrap">
      <button @click="toggleAllDirections" class="button is-secondary btn-changeAll">
        すべて{{ allDirection }}
      </button>
    </div> -->
    <div v-for="(block, index) in blocks" :key="index" class="row" :data-index="index">
      <div class="control">
        <div class="select">
          <select v-model="block.category">
            <option value=""></option>
            <option value="【Di】ヒアリング～TOP制作依頼">【Di】ヒアリング～TOP制作依頼</option>
            <option value="【DC】デザイン制作期間">【DC】デザイン制作期間</option>
            <option value="【DC】構築準備">【DC】構築準備</option>
            <option value="【Co】構築期間">【Co】構築期間</option>
            <option value="お客様確認＋修正期間">お客様確認＋修正期間</option>
            <option value="その他">その他</option>
          </select>
        </div>
      </div>
      <div :class="{'start-date': true, '-none': block.autoDate}">
        <input type="text" v-model="block.startDate" class="flatpickr input-date">
        <div class="icon-calendar"><i class="fa-solid fa-calendar-days"></i></div>
        <button v-if="index > 0" @click="toggleAutoDate(index)" class="btn-autoDate" :data-autoDate="block.autoDate">
          <i class="fa-solid fa-link"></i>
        </button>
      </div>
      から
      <div class="field has-addons m-0 days">
        <p class="control">
          <button @click="adjustBdays(index, -5)" class="button is-primary">-5</button>
        </p>
        <p class="control">
          <button @click="adjustBdays(index, -1)" class="button is-primary">-1</button>
        </p>
        <p class="control">
          <input type="number" min="0" v-model.number="block.bdays" class="input">
        </p>
        <p class="control">
          <button @click="adjustBdays(index, 1)" class="button is-primary">+1</button>
        </p>
        <p class="control">
          <button @click="adjustBdays(index, 5)" class="button is-primary">+5</button>
        </p>
      </div>
      営業日
      <button @click="toggleDirection(index)" class="button is-secondary">
        {{ block.direction }}
      </button>
      は
      <input type="text" :value="calculateResultDate(block.startDate, block.bdays, block.direction, index)" readonly class="input-date js-selectAllText">
      <!-- <input type="text" :value="block" readonly class="input-date js-selectAllText"> -->
      <div class="btns">
        <button @click="resetBlock(index)" class="button is-warning btn-clear">クリア</button>
        <button @click="removeBlock(index);" class="button is-danger btn-delete">削除</button>
      </div>
    </div>
    <button @click="addBlock" class="button is-success btn-add">追加</button>
    <div v-if="blocks.length > 0">
      <textarea name="" :value="getBlockSummaryText()" class="summary js-selectAllText"></textarea>
    </div>

    <div class="howto">
      <div class="howto__heading">■概要・使い方</div>
      <p class="howto__text">
        ・土日、祝日、会社の休日が反映された営業日計算ツールです。<br>
        ・2行目以降の日付入力欄は、上の行と連動しています。カレンダー上の鎖アイコンをクリックすると連動がOFFになり、自由に変更できるようになります。<br>
        ・「後」ボタンクリックで「前/後」を切替えできます。<br>
        ・計算結果を元に作業スケジュールが下のテキストエリアに出力されます。制作スケジュールの確認、共有などでご活用ください。
      </p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="./js/function.js?<?= $style_filemtime ?>"></script>
</body>
</html>
