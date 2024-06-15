<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>営業日計算ツール</title>
<link href="https://use.fontawesome.com/releases/v6.2.0/css/all.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="./css/style.css">
</head>
<body>
<header class="header">
  営業日計算ツール
</header>
<div id="app" class="wrap container mt-5">
  <div v-for="(block, index) in blocks" :key="index" class="row" :data-index="index">
    <input type="text" v-model="block.startDate" class="flatpickr input-date">
    <div class="icon-calendar"><i class="fa-solid fa-calendar-days"></i></div>
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
    <input type="text" :value="calculateResultDate(block.startDate, block.bdays, block.direction)" readonly class="input-date">
    <button @click="removeBlock(index)" class="button is-danger btn-delete">削除</button>
  </div>
  <button @click="addBlock" class="button is-success btn-add">追加</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
const { createApp } = Vue;

createApp({
  data() {
    return {
      blocks: [
        { startDate: '', bdays: 0, direction: '後' },
        { startDate: '', bdays: 0, direction: '後' },
        { startDate: '', bdays: 0, direction: '後' }
      ],
      holidays: []
    };
  },
  computed: {
    //
  },
  methods: {
    toggleDirection(index) {
      this.blocks[index].direction = this.blocks[index].direction === '後' ? '前' : '後';
    },
    adjustBdays(index, amount) {
      this.blocks[index].bdays = Math.max(0, this.blocks[index].bdays + amount);
    },
    calculateResultDate(startDate, bdays, direction) {
      if (!startDate || bdays === 0) return '';
      let start = new Date(startDate.replace(/\//g, '-'));
      let daysToAdd = bdays;
      while (daysToAdd > 0) {
        if (direction === '後') {
          start.setDate(start.getDate() + 1);
        } else if (direction === '前') {
          start.setDate(start.getDate() - 1);
        }
        let isWeekend = start.getDay() === 0 || start.getDay() === 6;
        let isHoliday = this.holidays.includes(start.toISOString().split('T')[0].replace(/-/g, '/'));
        if (!isWeekend && !isHoliday) {
          daysToAdd--;
        }
      }
      return start.toISOString().split('T')[0].replace(/-/g, '/');
    },
    addBlock() {
      this.blocks.push({ startDate: '', bdays: 0, direction: '後' });
      this.$nextTick(() => {
        this.initFlatpickr(); // 追加後にflatpickrを初期化
      });
    },
    removeBlock(index) {
      this.blocks.splice(index, 1);
    },
    initFlatpickr() {
      // flatpickrを初期化する
      const flatpickrElm = document.querySelectorAll('.flatpickr');
      flatpickr(flatpickrElm, {
        dateFormat: 'Y/m/d',
        onChange: (selectedDates, dateStr, instance) => {
          const index = instance.element.closest('.row').getAttribute('data-index');
          this.blocks[index].startDate = dateStr;
        }
      });
    }
  },
  created() {
    fetch('holidays.php')
      .then(response => response.text())
      .then(data => {
        this.holidays = data.split('\n').map(date => date.trim()).filter(date => date);
      })
      .catch(error => console.error('Error loading holidays:', error));
  },
  mounted() {
    this.initFlatpickr(); // 初回のflatpickr初期化
  },
}).mount('#app');
</script>
</body>
</html>
