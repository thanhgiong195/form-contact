jQuery(function ($) {
  var inputed = false;

  const LIST_LABEL = {
    title: 'お問い合わせ種別',
    customer_name: 'お名前',
    customer_kana: 'フリガナ',
    pref: '都道府県',
    address: '番地まで',
    tel: '電話番号',
    email: 'メールアドレス',
    mail_body: 'お問い合わせ内容',
    survey: '当社を何で知りましたか',
  };
  const REGEX = {
    email: /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    katakana: /[ァ-ンｧ-ﾝﾞﾟ]$/,
    phone: /^\d{2,3}-\d{3,4}-\d{3,4}$|^\d{10,11}$/,
  };

  // ページ離脱時にアラートを表示する
  function showUnloadMessage() {
    return OznForm.unload_message;
  }

  // ozn-form-nav クラスが付与されたリンクはアラートを出さない
  $('.ozn-form-nav').on('click', function () {
    $(window).off('beforeunload', showUnloadMessage);
  });

  /**
   * リアルタイム入力値検証
   */
  $.each(Object.keys(OznForm.forms), function () {
    var form_name = this;

    $('[name="' + form_name + '"]').on(
      'blur',
      { form_name: form_name },
      validateForm,
    );
  });

  // -- 設定に応じてオプション機能を追加

  /**
   * 郵便番号で住所補完機能（ajaxzip3）
   *
   * data-oznform-zip="keyword" 郵便番号フィールドの指定
   * data-oznform-pref="keyword" 都道府県入力フィールドの指定
   * data-oznform-address="keyword" 住所入力フィールドの指定
   *
   * ※ keyword を同じにすることにより各フィールドの関連付けを行う
   * ※ 都道府県入力フィールドが存在しない場合には、住所入力フィールドに全ての住所を入力する
   */
  (function () {
    // フォームキーワードを抽出して複数フォームの住所補完に対応
    var keywords = [];

    $('input[data-oznform-zip]').each(function () {
      keywords.push($(this).data('oznformZip'));
    });

    if (keywords.length > 0) {
      $.each($.unique(keywords), function () {
        var keyword = this;

        var $zip_fields = $('input[data-oznform-zip="' + keyword + '"]');
        var pref_elem_name = $(
          'input[data-oznform-pref="' + keyword + '"]',
        ).attr('name');
        var addr_elem_name = $(
          'input[data-oznform-address="' + keyword + '"]',
        ).attr('name');

        if (!pref_elem_name) {
          pref_elem_name = addr_elem_name;
        }

        if ($zip_fields.length == 1) {
          $zip_fields.on('keyup', function () {
            AjaxZip3.zip2addr(this, '', pref_elem_name, addr_elem_name);
          });
        } else {
          $($zip_fields[1]).on('keyup', function () {
            AjaxZip3.zip2addr(
              $($zip_fields[0]).attr('name'),
              $($zip_fields[1]).attr('name'),
              pref_elem_name,
              addr_elem_name,
            );
          });
        }
      });
    }
  })();

  /**
   * ふりがな自動入力機能（autoruby.js）
   *
   * data-autoruby="keyword" ふりがな自動入力する元の要素指定
   * data-autoruby-hiragana="keyword" ふりがな入力する要素の指定
   * data-autoruby-katakana="keyword" フリガナ入力する要素の指定
   *
   * ※ keyword を同じにすることにより各フィールドの関連付けを行う
   */
  (function () {
    var keywords = [];

    $('input[data-autoruby]').each(function () {
      keywords.push($(this).data('autoruby'));

      $.each(keywords, function () {
        var keyword = this;

        var $hiragana_ruby = $(
          'input[data-autoruby-hiragana="' + keyword + '"]',
        );
        var $katakana_ruby = $(
          'input[data-autoruby-katakana="' + keyword + '"]',
        );

        if ($hiragana_ruby.length == 1) {
          $.fn.autoKana(
            'input[data-autoruby="' + keyword + '"]',
            'input[data-autoruby-hiragana="' + keyword + '"]',
          );
        } else if (($katakana_ruby.length = 1)) {
          $.fn.autoKana(
            'input[data-autoruby="' + keyword + '"]',
            'input[data-autoruby-katakana="' + keyword + '"]',
            { katakana: true },
          );
        }
      });
    });
  })();

  /**
   * メールドメイン補完機能（domain_suggest.js）
   */
  (function () {
    $('[data-domain-suggest]').each(function () {
      var $target = $(this);
      var suggest_area_id = 'ozn-form-suggest-' + $target.data('domainSuggest');

      // suggest.js とのイベント競合を避けるため、リアルタイム検証処理を解除
      $target.off('blur', validateForm);

      // suggest.js の blur時のイベント処理に合わせて検証を実施
      // 選択後、文字挿入処理と同時ぐらいに検証イベントが発生するためちょっと遅らせて実行するようにした
      $target.on('SuggestJSBlurEvent', function () {
        setTimeout(function () {
          validateForm($target.attr('name'));
        }, 500);
      });

      // ブラウザの autocomplete 機能をOFF
      $target.attr('autocomplete', 'off');

      // サジェストリスト用の要素を用意
      $target.after(
        '<div id="' +
          suggest_area_id +
          '" class="ozn-form-suggest" style="display:none;"></div>',
      );

      new Suggest.Local(
        $target.get(0),
        suggest_area_id,
        window.OznForm.suggestMailList,

        // オプション
        {
          dispMax: 10,
          eventTarget: $target,
        },
      );
    });
  })();

  /**
   * 添付ファイル処理機能（jquery.fileupload.js）
   *
   *  data-oznform-fileup="keyword" アップロードフォームを挿入したい要素
   *
   *  ※ keyword はアップロードフォームのNAME値を指定
   *
   */
  (function () {
    var index = 1;

    $('[data-oznform-fileup]').each(function () {
      var $el = $(this);
      var form_name = $el.data('oznformFileup');

      var file_form_id = 'oznform-upform' + index;
      var file_btn_id = OznForm.utilities.uploadButtonElementName(form_name);
      var uploaded_files_id = OznForm.utilities.updatedFileElementName(
        form_name,
      );

      // ファイルアップロードフォームのテンプレート
      var upload_form_template =
        '\n' +
        '<span id="' +
        file_btn_id +
        '" class="fileinput-button" data-formname="' +
        form_name +
        '">' +
        '<button type="button">添付ファイル追加</button>' +
        '<input id="' +
        file_form_id +
        '" class="fileupload" type="file" name="files[]" multiple>' +
        '</span>' +
        '<div id="' +
        uploaded_files_id +
        '" class="oznform-uploaded-files"></div>';
      $el.append(upload_form_template);

      var upload_options = {
        dropZone: $(),
        url: OznForm.furl,
        dataType: 'json',
        done: function (e, data) {
          $.each(data.result.files, function (index, file) {
            var $files_el = $('#' + uploaded_files_id);
            if (file.error) {
              alert(file.error);
            } else {
              OznForm.utilities.addUploadFileElement(
                $files_el,
                form_name,
                file.thumbnailUrl,
                file.name,
                file.deleteUrl,
              );
            }
          });
        },
      };

      if (OznForm.client_resize_config.enableClientResize) {
        upload_options.imageOrientation = true;
        upload_options.processQueue = [
          {
            action: 'loadImageMetaData',
          },
          {
            action: 'loadImage',
            fileTypes: /^image\/(gif|jpeg|jpg|JPG|png)$/,
            maxFileSize: 10000000, // 10MB
          },
          {
            action: 'resizeImage',
            orientation: true,
            maxWidth: OznForm.client_resize_config.maxWidth,
            maxHeight: OznForm.client_resize_config.maxHeight,
          },
          {
            action: 'saveImage',
          },
        ];
      }

      $('#' + file_form_id).fileupload(upload_options);

      index++;
    });
  })();

  // 送信リンクの連続クリック防止
  OznForm.utilities.setSendmailButtonEvent($('.ozn-form-send'));

  // エンターキー押下時の送信を無効化する
  OznForm.utilities.disableEnterKeySubmit();

  // セッションに入っているデータをフォームに適用する
  OznForm.utilities.setSessionData(OznForm.page_data, OznForm.forms);

  /**
   * getで渡された値をフォームに初期値として挿入
   *
   * @note
   *  フォームルートのみの適用。
   *  すでにフォームに値がある場合は値を挿入しない。
   *
   */
  OznForm.utilities.setInitMessage(OznForm.init_msg);

  /**
   * Datepickerを適用する
   */
  $('[data-of_datepicker]').each(function () {
    $(this).datepicker();
  });

  /**
   * フォーム検証処理
   * @param {string} form_name
   */
  function validateForm(event) {
    var form_name = event;

    if (typeof event != 'string') {
      form_name = event.data.form_name;
    }

    if (OznForm.forms[form_name]['validates']) {
      validFormValue(form_name, OznForm.forms[form_name]);
    } else {
      setVaildMark($(this));
    }

    if (!inputed) {
      // 離脱アラートを表示（送信時は解除するため関連実装あり）
      if (OznForm.unload_message) {
        $(window).off('beforeunload', showUnloadMessage);
        $(window).on('beforeunload', showUnloadMessage);
      }

      inputed = true;
    }
  }

  // 送信時の入力値検証
  $('form').submit(validateAllForms);

  /**
   * 全てのフォームを検証処理する
   *
   * @returns {boolean}
   */
  function validateAllForms() {
    var $this = $(this);
    var ajax_validations = [];

    $.each(Object.keys(OznForm.forms), function () {
      var form_name = this;
      var form_config = OznForm.forms[form_name];
      var $form_el = $('[name="' + form_name + '"]');
      var is_fileup_form = form_config.type === 'upload_files';

      if ($form_el.length == 0 && !is_fileup_form) {
        return true;
      }

      if (
        !$form_el.hasClass('ozn-form-valid') &&
        OznForm.forms[form_name]['validates']
      ) {
        ajax_validations.push(validFormValue(form_name, form_config));
      } else if (!OznForm.forms[form_name]['validates']) {
        if (!is_fileup_form) {
          setVaildMark($form_el);
        }
      }
    });

    if (ajax_validations.length === 0) {
      // -- 全て検証OKの時の処理

      if (OznForm.reCAPTCHA) {
        grecaptcha.ready(function () {
          grecaptcha
            .execute(OznForm.reCAPTCHA_sitekey, { action: 'oznform' })
            .then(function (token) {
              $this.append(
                '<input type="hidden" name="g-recaptcha-response" value="' +
                  token +
                  '">',
              );

              $this.off('submit', validateAllForms);
              $(window).off('beforeunload', showUnloadMessage);
              $this.submit();
            });
        });
      } else {
        $this.off('submit', validateAllForms);
        $(window).off('beforeunload', showUnloadMessage);
        $this.submit();
      }
    } else {
      // 可変数のDeferredを並列実行させる
      $.when
        .apply($, ajax_validations)

        .done(function () {
          var results = this;
          var is_success = true;

          // 結果処理のため単値の場合は配列にする
          // deferred処理が一つのときは結果が配列で返ってこないため
          if (!$.isArray(results)) {
            results = [results];
          }

          $.each(results, function () {
            if (this == false) {
              is_success = false;
              return false;
            }
          });

          if (is_success) {
            // -- 全て検証OKの時の処理
            if (OznForm.reCAPTCHA) {
              grecaptcha.ready(function () {
                grecaptcha
                  .execute(OznForm.reCAPTCHA_sitekey, { action: 'oznform' })
                  .then(function (token) {
                    $this.append(
                      '<input type="hidden" name="g-recaptcha-response" value="' +
                        token +
                        '">',
                    );

                    $this.off('submit', validateAllForms);
                    $(window).off('beforeunload', showUnloadMessage);
                    $this.submit();
                  });
              });
            } else {
              $this.off('submit', validateAllForms);
              $(window).off('beforeunload', showUnloadMessage);
              $this.submit();
            }
          } else {
            // -- 検証NGの時の処理

            // 成功した要素を非表示にする
            hideValidItem();

            // 検証失敗したフォームまでスクロールバック
            var top_error_position = $('.ozn-form-invalid').eq(0).offset().top;

            $('html,body').animate({
              scrollTop:
                top_error_position + OznForm.vsetting.shift_scroll_position,
            });

            // 送信状態のボタンを元に戻す
            window.OznForm.utilities.clearSendingButtonStyle(
              $('.ozn-form-send'),
            );

            // 離脱アラートを設定する
            if (OznForm.unload_message) {
              $(window).off('beforeunload', showUnloadMessage);
              $(window).on('beforeunload', showUnloadMessage);
            }
          }
        })
        .fail(function () {
          alert('通信に失敗しました。');
        });
    }

    return false;
  }

  /**
   * フォーム入力値検証
   * @param form_name
   * @param form_config
   */
  function validFormValue(form_name, form_config) {
    var dInner = new $.Deferred();
    var t = [form_name];
    var $form_el = $('[name="' + form_name + '"]');
    var validate_condition = form_config.validate_condition;
    var post_condition = {};

    if (form_config.type === 'upload_files') {
      $form_el = $('#' + OznForm.utilities.uploadButtonElementName(form_name));
    }

    if (validate_condition) {
      t = t.concat(window.OznForm.utilities.objectKeys(validate_condition));

      $.each(validate_condition, function (key, value) {
        post_condition[key.replace('[]', '')] = value;
      });
    }

    var form_values = window.OznForm.utilities.getFormValues(t, true);

    form_name = form_name.replace('[]', '');

    var post_data = {
      name: form_name,
      values: form_values,
      label: form_config.label,
      error_messages: form_config.error_messages,
      condition: post_condition,
      validate: form_config.validates,
    };

    if (form_config.mobile_mail_warning) {
      post_data.mobile_warning = form_config.mobile_mail_warning;
    }

    clearInvalidMessages(form_name);

    var response = checkValidate(post_data);

    // 検証OKのときの処理
    if (response.valid) {
      if (response.warning) {
        setWarningMark($form_el, response.warning, form_config);
      } else {
        setVaildMark($form_el);
      }

      dInner.resolveWith(true);

      // 検証NGのときの処理
    } else {
      setInvalidMark($form_el, response.errors[form_name], form_config);
      dInner.resolveWith(false);
    }

    return dInner.promise();
  }

  function checkValidate(post_data) {
    const name = post_data.name;
    const value = post_data.values[name];
    const elmLabelError = LIST_LABEL[name];
    const validateJSON = {
      valid: true,
      warning: null,
      errors: {},
    };

    for (const valid of post_data.validate) {
      // check null
      if (valid == 'required' && !value) {
        validateJSON.valid = false;
        validateJSON.errors[name] = [`${elmLabelError}を入力してください`];
        break;
      }

      // check email
      if (valid == 'email_detail' && value) {
        if (!REGEX.email.test(String(value).toLowerCase())) {
          validateJSON.valid = false;
          validateJSON.errors[name] = [
            `${elmLabelError}に「@」マークが含まれていません`,
            `${elmLabelError}の形式が誤っています`,
          ];
          break;
        }
      }

      // check katakana
      if (valid == 'kanaOnly' && value) {
        if (!REGEX.katakana.test(value)) {
          validateJSON.valid = false;
          validateJSON.errors[name] = [
            `${elmLabelError}は「ひらがな」か「カタカナ」で入力してください`,
          ];
          break;
        }
      }

      // phone number
      if (valid == 'tel' && value) {
        if (!REGEX.phone.test(value)) {
          validateJSON.valid = false;
          validateJSON.errors[name] = [
            `${elmLabelError}は市外局番を含む数字またはハイフンの組み合わせで入力してください`,
          ];
          break;
        }
      }

      // survey
      if (valid == 'survey' && value) {
        if (value.length == 0) {
          validateJSON.valid = false;
          validateJSON.errors[name] = [`${elmLabelError}を入力してください`];
          break;
        }
      }
    }
    return validateJSON;
  }

  /**
   * 検証エラーメッセージ等を初期化
   */
  function clearInvalidMessages(form_name) {
    $('.' + form_name + '.ozn-form-errors').remove();
    $('.' + form_name + '.ozn-form-warning').remove();
  }

  /**
   * フォームを検証OKの表示にする
   * @param $form_el
   */
  function setVaildMark($form_el) {
    addResultClass($form_el, true);
    apendResultIcon($form_el, true);
  }

  /**
   * フォームを検証OKの表示にして、注意メッセージを表示する
   * @param $form_el
   * @param warning_message
   * @param form_config
   */
  function setWarningMark($form_el, warning_message, form_config) {
    addResultClass($form_el, true);
    apendErrorMessages($form_el, warning_message, form_config, true);
    apendResultIcon($form_el, true);
  }

  /**
   * フォームを検証NGの表示にして、エラーメッセージを表示する
   *
   * @param $form_el
   * @param error_message
   * @param form_config
   */
  function setInvalidMark($form_el, error_message, form_config) {
    addResultClass($form_el, false);
    apendErrorMessages($form_el, error_message, form_config, false);
    apendResultIcon($form_el, false);
  }

  /**
   * 検証結果に応じて検証結果判定クラスを付与する
   *
   * @note
   *   ozn-form-valid   : 検証OK
   *   ozn-form-invalid : 検証NG
   *
   * @param $el
   * @param is_valid
   */
  function addResultClass($el, is_valid) {
    // 対象要素がチェックボックスやラジオボタンの時は、ozn-check要素にクラスを追加する
    if ($.inArray($el.attr('type'), ['checkbox', 'radio']) >= 0) {
      var $ozn_check = $el.closest('.ozn-check');
      if ($ozn_check.length > 0) {
        $el = $ozn_check;
      }
    }

    if (is_valid) {
      $el.removeClass('ozn-form-invalid').addClass('ozn-form-valid');
    } else {
      $el.removeClass('ozn-form-valid').addClass('ozn-form-invalid');
    }
  }

  /**
   * エラーメッセージをページに挿入
   *
   * @param $el <フォーム要素>
   * @param msg <エラーメッセージ>
   * @param form_config <フォーム設定>
   * @param warning <注意メッセージとして表示フラグ>
   */
  function apendErrorMessages($el, msg, form_config, warning) {
    var form_name = OznForm.utilities.getFormNameByElement($el);
    var template = $('<div>' + msg.join('<br />') + '</div>');

    // エラー位置の指定があれば基準要素を置換
    if (form_config.error_message_position) {
      $el = $(form_config.error_message_position);

      // 対象要素がチェックボックスやラジオボタンの時は、ozn-checkをデフォルトとする
    } else if ($.inArray($el.attr('type'), ['checkbox', 'radio']) >= 0) {
      var $ozn_check = $el.closest('.ozn-check');
      if ($ozn_check.length > 0) {
        $el = $ozn_check;
      }
    }

    // エラーメッセージテンプレートがあればデフォルトテンプレートを置換
    if (form_config.error_message_template) {
      template = $(
        form_config.error_message_template.replace(
          '<% messages %>',
          msg.join('<br />'),
        ),
      );
    }

    // ドメインサジェスト表示エリアを取得
    var $suggest_area = $el.siblings('.ozn-form-suggest');

    // 付加するクラスを指定
    var added_class = 'ozn-form-errors';
    if (warning) {
      added_class = 'ozn-form-warning';
    }

    if ($suggest_area.length > 0) {
      $suggest_area.after(
        template.addClass(form_name.replace('[]', '') + ' ' + added_class),
      );
    } else {
      $el.after(
        template.addClass(form_name.replace('[]', '') + ' ' + added_class),
      );
    }
  }

  /**
   * 検証結果に応じたアイコン要素を挿入する
   *
   * @param {jQuery}  $el
   * @param {boolean} is_valid
   * @returns {number}
   */
  function apendResultIcon($el, is_valid) {
    var form_name = OznForm.utilities.getFormNameByElement($el);

    // 既存アイコンを初期化
    $('.' + form_name.replace('[]', '') + '.ozn-form-icon').remove();

    if (!OznForm.vsetting.show_icon) {
      return 1;
    }

    // 対象要素がチェックボックスやラジオボタンの時は、ozn-check要素の後ろにアイコン追加する
    if ($.inArray($el.attr('type'), ['checkbox', 'radio']) >= 0) {
      var $ozn_check = $el.closest('.ozn-check');
      if ($ozn_check.length > 0) {
        $el = $ozn_check;
      }
    }

    if (is_valid) {
      $el.after(
        '<i class="' +
          form_name.replace('[]', '') +
          ' ozn-form-icon icon-ok"></i>',
      );
    } else {
      $el.after(
        '<i class="' +
          form_name.replace('[]', '') +
          ' ozn-form-icon icon-caution"></i>',
      );
    }
  }

  /**
   * 検証OKの項目を非表示にする
   */
  function hideValidItem() {
    $('[data-oznform-area]').each(function () {
      var $this = $(this);
      var form_name = $this.data('oznformArea');
      var $form = $('[name="' + form_name + '"]');

      if (
        $form.hasClass('ozn-form-valid') ||
        $form.closest('.ozn-check').hasClass('ozn-form-valid')
      ) {
        // $this.hide();
      }
    });
  }
});
