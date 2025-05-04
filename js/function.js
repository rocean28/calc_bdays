const { createApp } = Vue;

createApp({
  data() {
    return {
      blocks: [
        { startDate: '', bdays: 0, direction: '後', autoDate: true, category: ''},
        { startDate: '', bdays: 0, direction: '後', autoDate: true, category: ''},
        { startDate: '', bdays: 0, direction: '後', autoDate: true, category: ''},
        { startDate: '', bdays: 0, direction: '後', autoDate: true, category: ''},
      ],
      allDirection: '後',
      holidays: [],
      flatpickrInstances: [],
      blockSummaryText: '',
    };
  },
  methods: {
    calcbdays: function(startDate, bdays, direction, index) {
      if (!startDate) return '';

      let date = startDate;
      let daysToAdd = bdays;
      while (daysToAdd > 0) {
        if (direction === '後') {
          date.setDate(date.getDate() + 1);
        } else if (direction === '前') {
          date.setDate(date.getDate() - 1);
        }
        let isWeekend = date.getDay() === 0 || date.getDay() === 6;
        let isHoliday = this.holidays.includes(date.getFullYear() + '/' + (date.getMonth() + 1) + '/' + date.getDate());
        if (!isWeekend && !isHoliday) {
          daysToAdd--;
        }
      }

      return date;
    },
    adjustBdays(index, amount) {
      this.blocks[index].bdays = Math.max(0, this.blocks[index].bdays + amount);
    },
    calculateResultDate(startDate, bdays, direction, index) {
      if (!startDate || bdays === 0) return '';

      // n営業日後の日付を計算
      let resultDate = this.calcbdays(new Date(startDate.replace(/\//g, '-')), bdays, direction);

      // 次の行の開始日を自動入力
      if (index < this.blocks.length - 1) {
        const nextIndex = index + 1;
        const nextBlock = this.blocks[nextIndex];
        this.$nextTick(() => {
          if (nextBlock.autoDate === true) {
            //+=1営業日の日付
            // nextBlock.startDate = this.calculateResultDate(resultDate.getFullYear() + '/' + (resultDate.getMonth() + 1) + '/' + resultDate.getDate(), 1, this.allDirection);
            //同じ日付
            nextBlock.startDate = resultDate.getFullYear() + '/' + (resultDate.getMonth() + 1) + '/' + resultDate.getDate();
          }
        });
      }

      endDate = resultDate.getFullYear() + '/' + (resultDate.getMonth() + 1) + '/' + resultDate.getDate();
      return endDate;
    },
    toggleAllDirections() {
      this.allDirection = this.allDirection === '後' ? '前' : '後';
      // let startDate;
      this.blocks.forEach(block => {
        block.direction = this.allDirection;
      });
    },
    toggleDirection(index) {
      this.toggleAllDirections();
      // this.blocks[index].direction = this.blocks[index].direction === '後' ? '前' : '後';
    },
    toggleAutoDate(index) {
      this.blocks[index].autoDate = this.blocks[index].autoDate === true ? false : true;
    },
    getBlockSummaryText() { //計算結果をスケジュールにして表示
      const blockSummaryText = [];
      this.blocks.forEach(block => {
        let startDate;
        let category = '';
        let firstDate = '';
        let lastDate = '';
        function getWeek(date) {
          const weekList = ['日', '月', '火', '水', '木', '金', '土'];
          return weekList[date.getDay()];
        }

        if(block.category) category = block.category + '　';

        if(block.startDate) {
          startDate = new Date(block.startDate.replace(/\//g, '-'));
          firstDate = this.calcbdays(startDate, 1, block.direction); //作業開始日は計算結果の1営業日後
          const firstDateWeek = getWeek(firstDate);
          firstDate = (firstDate.getMonth() + 1) + '/' + firstDate.getDate() + '(' + firstDateWeek + '）～ ';

          if(block.bdays) {
            startDate = new Date(block.startDate.replace(/\//g, '-')); //初期化
            endDate = this.calcbdays(startDate, block.bdays, block.direction);
            const endDateWeek = getWeek(endDate);
            lastDate = (endDate.getMonth() + 1) + '/' + endDate.getDate() + '(' + endDateWeek + '）';
          }
        }

        blockSummaryText.push(`${category}${firstDate}${lastDate}`);
      });

      return blockSummaryText.join('\n');
    },
    addBlock() {
      this.blocks.push({ startDate: '', bdays: 0, direction: this.allDirection, category: '', autoDate: true });
      this.$nextTick(() => {
        this.initFlatpickr(); // 追加後にflatpickrを初期化
      });
    },
    removeBlock(index) {
      this.blocks.splice(index, 1);
      this.$nextTick(() => {
        this.initFlatpickr(); // 削除後にflatpickrを初期化
      });
    },
    resetBlock(index) {
      this.blocks[index].startDate = '';
      this.blocks[index].bdays = 0;
      // this.blocks[index].direction = '後';
      this.blocks[index].category = '';
      this.$nextTick(() => {
        this.initFlatpickr(); // リセット後にflatpickrを初期化
      });
    },
    initFlatpickr() {
      // 既存のflatpickrインスタンスを破棄
      this.flatpickrInstances.forEach(instance => instance.destroy());
      this.flatpickrInstances = [];

      // 祝日を無効にする関数を定義
      const holidays = this.holidays.map(date => {
        // flatpickrのdate formatに合わせて変換
        const [year, month, day] = date.split('/').map(Number);
        return new Date(year, month - 1, day);
      });
      // 土日を無効にする関数を定義
      const disableWeekends = date => {
        const day = date.getDay();
        // 0: 日曜日, 6: 土曜日
        return day === 0 || day === 6;
      };
      // 祝日を無効にする関数を定義
      const disableHolidays = date => {
        return holidays.some(holiday =>
          date.getFullYear() === holiday.getFullYear() &&
          date.getMonth() === holiday.getMonth() &&
          date.getDate() === holiday.getDate()
        );
      };
      // flatpickrを初期化する
      const flatpickrElm = document.querySelectorAll('.flatpickr');

      flatpickrElm.forEach((element, index) => {
        const instance = flatpickr(element, {
          dateFormat: 'Y/n/j',
          locale: 'ja',
          disable: [
            disableWeekends,
            disableHolidays
          ],
          onChange: (selectedDates,
          dateStr, instance) => {
            const rowIndex = instance.element.closest('.row').getAttribute('data-index');
            this.blocks[rowIndex].startDate = dateStr;
          }
        });
        this.flatpickrInstances.push(instance);
      });
    },
  },
  created() {
    fetch('https://raw.githubusercontent.com/ezl-official-site/public_holidays/master/public_holidays.txt')
      .then(response => response.text())
      .then(data => {
        this.holidays = data.split('\n').map(date => date.trim()).filter(date => date);
        this.initFlatpickr();
      })
      .catch(error => console.error('Error loading holidays:', error));
  },
  mounted() {
    this.initFlatpickr(); // 初回のflatpickr初期化
  },
}).mount('#app');

// テキストエリアクリックでテキスト全選択
function selectAllText() {
  this.select();
}
const textareas = document.querySelectorAll('.js-selectAllText');
textareas.forEach(textarea => {
  textarea.addEventListener('click', selectAllText);
});