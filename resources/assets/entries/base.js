import findIndex from 'array.prototype.findindex';
import '../blocks/fontloader/fontloader';
import Vue from 'vue';

// устанавливаем полифил array.findIndex - глобально
findIndex.shim();
omx.global.window.EventBus = new Vue();
