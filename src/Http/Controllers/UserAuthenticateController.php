<?php

namespace Omadonex\LaravelSupport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Omadonex\LaravelSupport\Classes\ConstantsCustom;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;
use Omadonex\LaravelSupport\Models\UserAuthenticate;
use Omadonex\LaravelSupport\Traits\UserUtmTrait;

class UserAuthenticateController extends Controller
{
    /**
     * Авторизация через соц. сети
     * Используется ulogin для получения данных о соц. сети пользователя
     */
    public function social(IUserService $userService) {
        //Отправляем запрос на сайт ulogin
        $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
        //ulogin возвращает строку с данными, после декодирования - это массив
        $identities = json_decode($s, true);
        //в массиве данных есть обязательное поле - проверенный email. Если он не проверен - ошибка.
        if ($identities['verified_email'] == -1) {
            abort(403);
        }

        //ищем пользователя в таблице users с таким емэйлом
        $user = User::where('email', $identities['email'])->first();
        if (is_null($user)) {
            //юзера не нашли, создаем нового, генерируем случайный пароль, имя берем из социалок
            $user = $userService->createExt([
                'email' => $identities['email'],
                //'url_photo' => $identities['photo_big'],
                //$identities['first_name'] . ' ' . $identities['last_name']
            ], true)['user'];
            //емэйл из социалок или из введенных данных, так как он проверен.

            if (in_array(UserUtmTrait::class, class_uses(User::class))) {
                $user->setUtm();
            }

            //$this->setReferral($user);
            //$this->setPartner($user);
            //создаем новую запись в таблице аутентификаций, соответствующую выбранной социалке.
            UserAuthenticate::create([
                'user_id' => $user->id,
                'network' => $identities['network'],
                'uid' => $identities['uid'],
                'identity' => $identities['identity'],
                'profile' => $identities['profile']
            ]);
            //создаем директории
            //$this->createDirs($user->id);
            //отправляем пользователю письмо с данными созданной стандартной учетки
            //$this->sendRegisterMail(['name' => $user->name, 'email' => $user->email, 'password' => $password]);
        } else {
            //если такой пользователь найден, то ищем его аутентификацию в таблцице
            $authenticate = UserAuthenticate::where('network', $identities['network'])->where('uid', $identities['uid'])->first();
            if (is_null($authenticate)) {
                //если аутентификация не найдена, то создаем новую
                UserAuthenticate::create([
                    'user_id' => $user->id,
                    'network' => $identities['network'],
                    'uid' => $identities['uid'],
                    'identity' => $identities['identity'],
                    'profile' => $identities['profile']
                ]);
            }
        }

        //логиним юзера
        Auth::login($user, true);

        $lang = app('locale')->getCurrLanguage();

        return redirect("/{$lang}");
    }
}
