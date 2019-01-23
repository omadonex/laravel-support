import findIndex from 'array.prototype.findindex';
import '../blocks/fontloader/fontloader';

// устанавливаем полифил array.findIndex - глобально
findIndex.shim();
