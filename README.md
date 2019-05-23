1. POST /api/user/add – добавление нового пользователя
Обязательные параметры: логин (номер телефона) и пароль.
Необязательные параметры: email, имя, фамилия и права.
Возможные права:
- чтение раздела A,
- редактирование раздела A,
- чтение раздела B,
- редактирование раздела B.
Предусмотреть расширения списка пермишенов в будущем.

2. GET /api/user/check – проверка пользователя по логину и паролю.

3. GET /api/user/info – получить информацию о пользователе.

4. POST /api/user/update – обновление данных пользователя.