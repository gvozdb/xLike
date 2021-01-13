(function () {
    function xLike(options) {
        //
        [/*'assetsUrl', */'actionUrl'].forEach(function (val, i, arr) {
            if (typeof(options[val]) == 'undefined' || options[val] == '') {
                console.error('[xLike] Bad config.', arr);
                return;
            }
        });

        //
        var self = this;
        self.initialized = false;
        self.running = false;

        /**
         * Инициализирует класс.
         * @returns {boolean}
         */
        self.initialize = function (options) {
            if (!self.initialized) {
                //
                self.config = {};
                self.classes = {
                    buttonActive: 'xlike__link_active',
                };
                self.selectors = {
                    object: '.js-xlike-object',
                    button: '.js-xlike-button',
                    number: '.js-xlike-number',
                    rating: '.js-xlike-rating',
                    stripe: '.js-xlike-stripe',
                };
                self.sendDataTemplate = {
                    $element: null,
                    params: null,
                };
                self.sendData = $.extend({}, self['sendDataTemplate']);

                //
                Object.keys(options).forEach(function (key) {
                    if (['selectors'].indexOf(key) !== -1) {
                        return;
                    }
                    self.config[key] = options[key];
                });

                //
                ['selectors'].forEach(function (key) {
                    if (options[key]) {
                        Object.keys(options[key]).forEach(function (i) {
                            self.selectors[i] = options.selectors[i];
                        });
                    }
                });
            }
            self['initialized'] = true;

            return self.initialized;
        };

        /**
         * Запускает основные действия.
         * @returns {boolean}
         */
        self.run = function () {
            if (self['initialized'] && !self['running']) {
                /**
                 * Клик по кнопке
                 */
                $(document).on('click', self.selectors['button'], function (e) {
                    e.preventDefault();

                    var $button = $(this);
                    var $object = $button.closest(self.selectors['object']);
                    if ($object['length']) {
                        var propkey = $object.data('xlike-propkey');
                        var props = $object.data('xlike-props');
                        var parent = $object.data('xlike-parent');
                        var value = $button.data('xlike-value');
                        var sendData = $.extend({}, self['sendDataTemplate']);

                        // Готовим параметры запроса
                        sendData['$element'] = $button;
                        sendData['params'] = {
                            action: 'vote',
                            propkey: propkey,
                            props: props,
                            parent: parent,
                            value: value,
                        };

                        // Если уже есть активная кнопка - запомним в параметры
                        var $last = $object.find(self.selectors['button']).filter('.' + self.classes['buttonActive']);
                        if ($last['length'] && !$last.data('xlike-tmp')) {
                            // console.log('[xLike] on(click) $last', $last);
                            sendData['$last'] = $last;
                        }

                        // console.log(sendData);

                        // Шлём запрос
                        self['sendData'] = $.extend({}, sendData);
                        self.Submit.post();
                    }
                });
            }
            self.running = true;

            return self.running;
        };

        /**
         * Отсылает запрос на сервер.
         * @type {object}
         */
        self.Submit = {
            // status: false,
            before: function () {
                if (self.sendData.params['action'] == 'vote') {
                    // Реализуем оптимистичный интерфейс
                    var $last = self.sendData['$last'];
                    var $button = self.sendData['$element'];
                    var $object = $button.closest(self.selectors['object']);
                    var $number = $button.find(self.selectors['number']);
                    var number = parseInt(($number.text()).replace(' ', '').replace(',', ''));
                    // var number = $number.text();
                    // number = parseInt(number.replace(' ', '').replace(',', ''));

                    $object.find(self.selectors['button']).each(function (idx, el) {
                        var $btn = $(el);
                        if ($button.get(0) != $btn.get(0)) {
                            if ($btn.hasClass(self.classes['buttonActive'])) {
                                $btn.removeClass(self.classes['buttonActive']); // .removeAttr('data-xlike-tmp')
                                var $num = $btn.find(self.selectors['number']);
                                var num = parseInt(($num.text()).replace(' ', '').replace(',', ''));
                                // var num = $num.text();
                                // num = parseInt(num.replace(' ', '').replace(',', ''));
                                $num.text(self.Tools.number_format((num - 1), 0, '.', ' '));
                            }
                        }
                    });
                    $button.toggleClass(self.classes['buttonActive']).data('xlike-tmp', 1);
                    if ($button.hasClass(self.classes['buttonActive'])) {
                        $number.text(self.Tools.number_format((number + 1), 0, '.', ' '));
                    } else {
                        $number.text(self.Tools.number_format((number - 1), 0, '.', ' '));
                    }
                }
            },
            after: function (response) {
                if (self.sendData.params['action'] == 'vote') {
                    var $last = self.sendData['$last'];
                    var $button = self.sendData['$element'];
                    var $object = $button.closest(self.selectors['object']);
                    var $number = $button.find(self.selectors['number']);
                    var number = parseInt(($number.text()).replace(' ', '').replace(',', ''));

                    // При безуспешном запросе
                    if (!response['success']) {
                        // 1) Переключаем класс активной кнопки
                        $button.toggleClass(self.classes['buttonActive']).removeAttr('data-xlike-tmp').removeData('xlike-tmp');

                        // 2) Возвращаем предыдущее значение голосов
                        if ($button.hasClass(self.classes['buttonActive'])) {
                            $number.text(self.Tools.number_format((number + 1), 0, '.', ' '));
                        } else {
                            $number.text(self.Tools.number_format((number - 1), 0, '.', ' '));
                        }

                        // 3) Возвращаем класс активной кнопки последнему элементу
                        if (!!$last && $last['length'] && $last[0] != $button[0]) {
                            var $last_number = $last.find(self.selectors['number']);
                            var last_number = parseInt(($last_number.text()).replace(' ', '').replace(',', ''));

                            // 3.1) Переключаем класс активной кнопки на последний активный
                            $last.toggleClass(self.classes['buttonActive']).removeAttr('data-xlike-tmp').removeData('xlike-tmp');

                            // 3.2) Возвращаем предыдущее значение голосов последней активной
                            if ($last.hasClass(self.classes['buttonActive'])) {
                                $last_number.text(self.Tools.number_format((last_number + 1), 0, '.', ' '));
                            } else {
                                $last_number.text(self.Tools.number_format((last_number - 1), 0, '.', ' '));
                            }
                        }
                    }
                    // При успешном запросе
                    else {
                        var $rating = $object.find(self.selectors['rating']);
                        var $stripe = $object.find(self.selectors['stripe']);

                        // 1) Удаляем обозначение временного назначения класса
                        $button.removeAttr('data-xlike-tmp');

                        // 2) Ставим новое значение рейтинга
                        if (typeof(response.data['rating']) != 'undefined') {
                            var rating_old = $rating.text().replace(' ', ''); // parseFloat($rating.text().replace(' ', ''));
                            var rating_new = response.data['rating']; // parseFloat(response.data['rating']);
                            console.log('[xLike] self.Submit.post() after rating_old', rating_old);
                            console.log('[xLike] self.Submit.post() after rating_new', rating_new);

                            // $rating.text(rating_new);

                            self.Tools.animateNumbers($rating, rating_old, rating_new, 700);
                        }

                        // 3) Ставим новую длину полосы
                        if (typeof(response.data['rating']) != 'undefined') {
                            $stripe.css({minWidth: response.data['rating'] + '%'});
                        }
                    }
                }
            },
            post: function (callback) {
                if (!self.sendData['params'] || !self.sendData.params['action']) {
                    return;
                }
                self.Submit.before();

                $.post(self.config['actionUrl'], self.sendData['params'], function (response) {
                    console.log('xLike self.Submit.post() response', response);

                    self.Submit.after(response);

                    if (response['success']) {
                        self.Message.success(response['message']);

                        // Запускаем колбек
                        if (callback && $.isFunction(callback)) {
                            callback.call(this, response, self.sendData['params']);
                        }
                    } else {
                        self.Message.error(response['message']);
                    }
                }, 'json')
                    .fail(function () {
                        console.error('[xLike] Bad request.', self['sendData']);
                    })
                    .done(function () {
                    });
            },
        };

        /**
         * Сообщения.
         * @type {object}
         */
        self.Message = {
            success: function (message) {
            },
            error: function (message) {
                alert(message);
            }
        };

        /**
         * Инструменты.
         * @type {object}
         */
        self.Tools = {
            /**
             * Аналог number_format из PHP
             *
             * @param number
             * @param decimals
             * @param dec_point
             * @param thousands_sep
             */
            number_format: function (number, decimals, dec_point, thousands_sep) {
                var i, j, kw, kd, km;

                if (isNaN(decimals = Math.abs(decimals))) {
                    decimals = 2;
                }
                if (dec_point == undefined) {
                    dec_point = ',';
                }
                if (thousands_sep == undefined) {
                    thousands_sep = '.';
                }

                i = parseInt(number = (+number || 0).toFixed(decimals)) + '';
                if ((j = i.length) > 3) {
                    j = j % 3;
                } else {
                    j = 0;
                }
                km = j ? (i.substr(0, j) + thousands_sep) : '';
                kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
                kd = (decimals ? (dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, '0').slice(2)) : '');

                return (km + kw + kd);
            },

            /**
             * Анимированные числа
             *
             * @param element
             * @param start
             * @param stop
             * @param duration
             * @param easing
             */
            animateNumbers: function (element, start, stop, duration, easing) {
                if (start != stop) {
                    var $element = element;
                    $({value: start}).animate({value: stop}, {
                        duration: (duration == undefined) ? 1000 : duration,
                        easing: (easing == undefined) ? 'swing' : easing,
                        step: function () {
                            $element.text(self.Tools.number_format(this['value'], 2, '.', ''));
                        },
                        complete: function () {
                            $element.text(stop);
                        }
                    });
                }
            },
        };

        /**
         * Initialize && Run!
         */
        if (self.initialize(options)) {
            self.run();
        }
    }

    window.xLike = xLike;
})();