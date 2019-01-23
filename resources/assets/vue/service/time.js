import moment from 'moment-timezone';
import DateTimeFormats from './__config/time';

const TimeService = {
  data() {
    return {
      Data__time: {
        formatCategories: {
          MOMENT: 'moment',
          ELEMENT: 'element',
        },
        formatTypes: {
          DATE_TIME_ZONE: 'date_time_zone',
          DATE_TIME_ZONE_S: 'date_time_zone_s',
          DATE_TIME: 'date_time',
          DATE: 'date',
          TIME: 'time',
        },
        formats: DateTimeFormats,
      },
      moment: moment,
    };
  },

  methods: {
    time__setFormats(formats) {
      this.Data__time.formats = formats;
    },

    time__getFormat(formatCategory, formatType) {
      return this.Data__time.formats[formatCategory][this.$root.DataLang.currLang][formatType];
    },

    time__getMomentFormat(formatType) {
      return this.time__getFormat(this.Data__time.formatCategories.MOMENT, formatType);
    },

    time__getElementFormat(formatType) {
      return this.time__getFormat(this.Data__time.formatCategories.ELEMENT, formatType);
    },

    time__getElementFormatDate() {
      return this.time__getElementFormat(this.Data__time.formatTypes.DATE);
    },

    time__getElementFormatDateTime() {
      return this.time__getElementFormat(this.Data__time.formatTypes.DATE_TIME);
    },

    time__getElementFormatTime() {
      return this.time__getElementFormat(this.Data__time.formatTypes.TIME);
    },

    time__now() {
      return +this.moment().tz(this.$root.DataSettings.tz);
    },

    time__phpTsToJs(phpTs) {
      return +this.moment.unix(phpTs).tz(this.$root.DataSettings.tz);
    },

    time__jsTsToPhp(jsTs) {
      return +this.moment(jsTs).utc().unix();
    },

    time__phpTsToStr(phpTs, formatType) {
      let finalFormatType = formatType || this.Data__time.formatTypes.DATE_TIME_ZONE;

      return this.moment.unix(phpTs).tz(this.$root.DataSettings.tz).format(this.time__getMomentFormat(finalFormatType));
    },

    time__phpTsToDateTimeZoneStr(phpTs) {
      return this.time__phpTsToStr(phpTs, this.Data__time.formatTypes.DATE_TIME_ZONE);
    },

    time__phpTsToDateTimeZoneSStr(phpTs) {
      return this.time__phpTsToStr(phpTs, this.Data__time.formatTypes.DATE_TIME_ZONE_S);
    },

    time__phpTsToDateTimeStr(phpTs) {
      return this.time__phpTsToStr(phpTs, this.Data__time.formatTypes.DATE_TIME);
    },

    time__phpTsToDateStr(phpTs) {
      return this.time__phpTsToStr(phpTs, this.Data__time.formatTypes.DATE);
    },

    time__phpTsToTimeStr(phpTs) {
      return this.time__phpTsToStr(phpTs, this.Data__time.formatTypes.TIME);
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(TimeService);
  },
};
