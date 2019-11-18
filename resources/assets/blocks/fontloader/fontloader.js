import FontFaceObserver from 'fontfaceobserver';

const timeout = 10 * 1000;
const fontFamiliesList = {
  'Open Sans': [
    {weight: 400},
    {weight: 600},
    {weight: 700},
  ],
  'Material Icons': [],
};

Object.keys(fontFamiliesList)
  .forEach((family) => {
    const familyName = family.replace(/\W/g, '-').toLowerCase();
    const familyLoadedClass = `webfont-${familyName}-loaded`;
    // если есть класс, поддтверждающий что все шрифты семейства загружены, переходим к следующему
    if (document.documentElement.classList.contains(familyLoadedClass)) {
      return;
    }
    // собираем группу промисов загрузки шрифтов семейства
    const familyObservList = fontFamiliesList[family]
      .map(config => new FontFaceObserver(family, config).load(null, timeout));
    // загружаем всю группу
    Promise.all(familyObservList)
      .then(() => {
        // загрузились все шрифты группы
        document.documentElement.classList.add(familyLoadedClass);
        const date = new Date(new Date().getTime() + 60000000);
        document.cookie = `webfont-${familyName}-loaded=true; path=/; expires=${date.toUTCString()}`;
      })
      .catch((error) => {
        console.log(error);
      });
  });
