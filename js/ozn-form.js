jQuery(function ($) {
  var inputed = false;
  let ContactForm = {};
  ContactForm.page_role = "form";
  ContactForm.page_data = [];
  ContactForm.vsetting = { show_icon: true, shift_scroll_position: -50 };
  ContactForm.forms = {
    customer_name: {
      label: "お名前",
      validates: ["required"],
      error_messages: [],
      validate_condition: [],
    },
    customer_kana: {
      label: "フリガナ",
      validates: ["required", "kanaOnly"],
      error_messages: [],
      validate_condition: [],
    },
    tel: {
      label: "電話番号",
      validates: ["required", "tel"],
      error_messages: [],
      validate_condition: [],
    },
    email: {
      label: "お問い合わせ詳細",
      validates: ["required", "email_detail"],
      mobile_mail_warning:
        "携帯電話のアドレスが入力されました。ozonenotes.jp ドメインからのメールを受信できるように設定お願いします。",
      error_messages: [],
      validate_condition: [],
    },
    content: {
      label: "お問合せ内容",
      validates: [],
      error_messages: [],
      validate_condition: [],
    },
    class_room: {
      label: "興味のある商品",
      validates: [],
      error_messages: [],
      validate_condition: [],
    },
    gakkou: {
      label: "生徒の学校",
      validates: [],
      error_messages: [],
      validate_condition: [],
    },
  };
  ContactForm.utilities = {
    /**
     * PHPセッションに保存済みのデータをフォームに適用する
     *
     * @param {object} session_data <セッションに保存済みのデータ>
     * @param {object} forms        <フォーム設定>
     */
    setSessionData: function (session_data, forms) {
      var self = this;

      $.each(session_data, function (name, value) {
        // アップロードフォームの場合はファイルの情報を取得する
        if (forms[name]["type"] === "upload_files") {
          if (value) {
            $.each(value, function () {
              self.setUploadedFile(name, this);
            });
          }
        } else {
          var $elem = $('[name="' + name + '"]');
          self.setValue($elem, value);
        }
      });
    },

    /**
     * 各フォームの初期値を設定する
     *
     * @param messages
     */
    setInitMessage: function (messages) {
      var self = this;

      $.each(messages, function (name, value) {
        // 設定値が配列の場合は [] をつける
        if ($.isArray(value)) {
          name += "[]";
        }

        var $elem = $('[name="' + name + '"]');

        if ($elem.length > 0) {
          switch ($elem.attr("type")) {
            case "radio":
            case "checkbox":
              if ($elem.filter("checked").length === 0) {
                self.setValue($elem, value);
              }
              break;
            default:
              if ($elem.val() === "") {
                self.setValue($elem, value);
              }
          }
        }
      });
    },

    setValue: function ($elem, value) {
      // 入力済みの値を要素に適用
      if ($elem.attr("type") && !(value instanceof Array)) {
        $elem.val([value]);
      } else {
        $elem.val(value);
      }
    },

    /**
     * 送信リンクの二重クリックの防止
     *
     * @param {JQuery} $el <送信用リンク要素>
     */
    setSendmailButtonEvent: function ($el) {
      $el.on("click", function () {
        var $this = $(this);

        if ($this.hasClass("contact-form-disabled")) {
          return false;
        }

        window.ContactForm.submitLabel = $this.text();

        var send_message = "送信中です…お待ちください。";

        if ($this.data("message")) {
          send_message = $this.data("message");
        }

        $this.text(send_message);
        $this.addClass("contact-form-disabled disabled");

        // ナビ要素にもクラスを付与する
        $(".contact-form-nav").addClass("contact-form-sending");
      });
    },

    clearSendingButtonStyle: function ($el) {
      if (!$el.hasClass("contact-form-disabled")) {
        return false;
      }

      $el.text(window.ContactForm.submitLabel);
      $el.removeClass("contact-form-disabled disabled");

      $(".contact-form-nav").removeClass("contact-form-sending");
    },

    getFormValues: function (names, removeBrackets) {
      if (!$.isArray(names)) {
        names = [names];
      }

      var form_values = {};

      $.each(names, function () {
        var name = this;

        var $form_el = $('[name="' + name + '"]');
        var form_value = $form_el.val();
        var form_config = window.ContactForm.forms[name];

        var is_upfile_form = false;

        if (form_config) {
          is_upfile_form = form_config.type === "upload_files";
        }

        // -- 各フォームタイプにより取得値などの設定を変更する

        // ファイルアップロードフォームの場合
        if (is_upfile_form) {
          var fileup_element_id = ContactForm.utilities.updatedFileElementName(name);

          if ($("#" + fileup_element_id).find("input").length > 0) {
            form_value = "check_ok";
          } else {
            form_value = "";
          }

          // 通常フォームの場合
        } else {
          if ($form_el.attr("type") === "radio") {
            // ラジオボタンの時は、チェックされているデータを送信する
            form_value = $form_el.filter(":checked").val();
          } else if ($form_el.attr("type") === "checkbox") {
            // チェックボックスの時にはチェックされているすべてのデータを送信する（配列）
            form_value = [];
            $form_el.filter(":checked").each(function () {
              form_value.push($(this).val());
            });
          } else if ($form_el.prop("nodeName") === "INPUT") {
            // その他の input 要素の時は全角を半角に変換して送信する

            // 全角半角変換
            form_value = ContactForm.utilities.toHalfWidth(form_value);

            // 半角カナ全角カナ変換（カナ検証の時のみ）
            if ($.inArray("kanaOnly", form_config.validates) !== -1) {
              form_value = ContactForm.utilities.hankana2zenkana(form_value);
            }

            // フォームのユーザ入力値を半角変換済みの値に修正
            // ※ 設定で明示的に false を指定した場合はスキップ
            if (form_config.to_half !== false) {
              $form_el.val(form_value);
            }
          }
        }

        if (removeBrackets) {
          name = name.replace("[]", "");
        }

        form_values[name] = form_value;
      });

      return form_values;
    },

    /**
     * 要素からフォームのNAME値を取得する
     * @param $el
     * @returns {string}
     */
    getFormNameByElement: function ($el) {
      var name = $el.attr("name");

      if (name === undefined) {
        name = $el.data("formname");
      }

      return name;
    },

    /**
     * NAME値からフォーム要素を取得する
     *
     * @param {String} name <NAME値>
     * @returns {*|jQuery|HTMLElement} <取得したフォーム要素の jQuery Object>
     */
    getFormElementByName: function (name) {
      return $('[name="' + name + '"]');
    },

    /**
     * 入力されたフォーム要素の値を返す
     *
     * @param {String|jQuery} $elem
     * @returns {String|null} <選択/入力されたフォームのVALUE値>
     */
    getEnteredValue: function ($elem) {
      if (typeof $elem == "string") {
        $elem = this.getFormElementByName($elem);
      }

      switch ($elem.attr("type")) {
        case "radio":
        case "checkbox":
          return $elem.filter(":visible:checked").val();
          break;
        default:
          return $elem.val();
      }
    },

    /**
     * 全角から半角への変換
     * 入力値の英数記号を半角変換して返却
     * [引数]   strVal: 入力値
     * [返却値] String(): 半角変換された文字列
     */
    toHalfWidth: function (strVal) {
      // 半角変換
      var halfVal = strVal.replace(/[！-～]/g, function (tmpStr) {
        // 文字コードをシフト
        return String.fromCharCode(tmpStr.charCodeAt(0) - 0xfee0);
      });

      return halfVal;
    },

    /**
     * 半角カタカナを全角カタカナに変換
     *
     * @refer http://qiita.com/hrdaya/items/291276a5a20971592216
     *
     * @param {String} str 変換したい文字列
     */
    hankana2zenkana: function (str) {
      var kanaMap = {
        ｶﾞ: "ガ",
        ｷﾞ: "ギ",
        ｸﾞ: "グ",
        ｹﾞ: "ゲ",
        ｺﾞ: "ゴ",
        ｻﾞ: "ザ",
        ｼﾞ: "ジ",
        ｽﾞ: "ズ",
        ｾﾞ: "ゼ",
        ｿﾞ: "ゾ",
        ﾀﾞ: "ダ",
        ﾁﾞ: "ヂ",
        ﾂﾞ: "ヅ",
        ﾃﾞ: "デ",
        ﾄﾞ: "ド",
        ﾊﾞ: "バ",
        ﾋﾞ: "ビ",
        ﾌﾞ: "ブ",
        ﾍﾞ: "ベ",
        ﾎﾞ: "ボ",
        ﾊﾟ: "パ",
        ﾋﾟ: "ピ",
        ﾌﾟ: "プ",
        ﾍﾟ: "ペ",
        ﾎﾟ: "ポ",
        ｳﾞ: "ヴ",
        ﾜﾞ: "ヷ",
        ｦﾞ: "ヺ",
        ｱ: "ア",
        ｲ: "イ",
        ｳ: "ウ",
        ｴ: "エ",
        ｵ: "オ",
        ｶ: "カ",
        ｷ: "キ",
        ｸ: "ク",
        ｹ: "ケ",
        ｺ: "コ",
        ｻ: "サ",
        ｼ: "シ",
        ｽ: "ス",
        ｾ: "セ",
        ｿ: "ソ",
        ﾀ: "タ",
        ﾁ: "チ",
        ﾂ: "ツ",
        ﾃ: "テ",
        ﾄ: "ト",
        ﾅ: "ナ",
        ﾆ: "ニ",
        ﾇ: "ヌ",
        ﾈ: "ネ",
        ﾉ: "ノ",
        ﾊ: "ハ",
        ﾋ: "ヒ",
        ﾌ: "フ",
        ﾍ: "ヘ",
        ﾎ: "ホ",
        ﾏ: "マ",
        ﾐ: "ミ",
        ﾑ: "ム",
        ﾒ: "メ",
        ﾓ: "モ",
        ﾔ: "ヤ",
        ﾕ: "ユ",
        ﾖ: "ヨ",
        ﾗ: "ラ",
        ﾘ: "リ",
        ﾙ: "ル",
        ﾚ: "レ",
        ﾛ: "ロ",
        ﾜ: "ワ",
        ｦ: "ヲ",
        ﾝ: "ン",
        ｧ: "ァ",
        ｨ: "ィ",
        ｩ: "ゥ",
        ｪ: "ェ",
        ｫ: "ォ",
        ｯ: "ッ",
        ｬ: "ャ",
        ｭ: "ュ",
        ｮ: "ョ",
        "｡": "。",
        "､": "、",
        ｰ: "ー",
        "｢": "「",
        "｣": "」",
        "･": "・",
      };

      var reg = new RegExp("(" + Object.keys(kanaMap).join("|") + ")", "g");
      return str
        .replace(reg, function (match) {
          return kanaMap[match];
        })
        .replace(/ﾞ/g, "゛")
        .replace(/ﾟ/g, "゜");
    },

    /**
     * input 要素でエンター押下時に送信を無効にする
     */

    disableEnterKeySubmit: function () {
      $("input").on("keydown", function (e) {
        if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
          return false;
        } else {
          return true;
        }
      });
    },

    /**
     * ファイルアップロード後の表示エレメントを追加する
     *
     * @param {jQuery} $target <挿入する要素>
     * @param {string} form_name <フォームの名前>
     * @param {string} thumbnail_url <ThumbnailのURL>
     * @param {string} file_name <アップロードファイル名>
     * @param {string} delete_url <削除用URL>
     */
    addUploadFileElement: function ($target, form_name, thumbnail_url, file_name, delete_url) {
      // テンプレート生成
      var template = [];

      template.push('<div class="contactform-uploaded-file">');

      if (thumbnail_url) {
        template.push('<span class="contactform-uploaded-thumbnail"><img src="' + thumbnail_url + '"></span>');
      }

      template.push('<span class="contactform-uploaded-filename">' + file_name + "</span>");
      template.push(
        '<button class="contactform-delete-file" type="button" data-delete-url="' + delete_url + '">削除</button>',
      );
      template.push('<input type="hidden" name="' + form_name + '" value="' + file_name + '">');
      template.push("</div>");

      var $file_el = $(template.join("\n"));

      // 削除処理をバインド
      $file_el.find("[data-delete-url]").on("click", function () {
        var $el = $(this);

        $.ajax({
          type: "post",
          url: delete_url,
        }).always(function () {
          $el.parent().remove();
        });
      });

      // 対象要素に追加
      $target.append($file_el);
    },

    /**
     * アップロードファイル情報を表示する要素名を生成する
     *
     * @param {string} form_name <対象フォームのNAME値>
     */
    updatedFileElementName: function (form_name) {
      form_name = form_name.replace("[]", "");
      return form_name + "_files";
    },

    uploadButtonElementName: function (form_name) {
      form_name = form_name.replace("[]", "");
      return "upload-" + form_name + "-button";
    },

    /**
     * アップロードされたファイル情報を表示用要素へセットする
     *
     * @param {string} name  <対象フォームのNAME値>
     * @param {string} value <ファイル名>
     */
    setUploadedFile: function (name, value) {
      var self = this;
      var file_name = encodeURIComponent(value);
      var check_url = ContactForm.furl + "?file=" + file_name;

      $.ajax({
        type: "get",
        url: check_url,
      }).done(function (res) {
        res = $.parseJSON(res);

        self.addUploadFileElement(
          $("#" + self.updatedFileElementName(name)),
          name,
          res.file.thumbnailUrl,
          res.file.name,
          res.file.deleteUrl,
        );
      });
    },

    objectKeys: function (obj) {
      return $.map(obj, function (value, key) {
        return key;
      });
    },
  };

  const LIST_LABEL = {
    title: "お問い合わせ種別",
    customer_name: "お名前",
    customer_kana: "フリガナ",
    pref: "都道府県",
    address: "番地まで",
    address_building: "建物名等",
    tel: "電話番号",
    email: "メールアドレス",
    survey: "当社を何で知りましたか",
    content: "お問合せ内容",
    class_room: "生徒の学年",
    course: "ご希望の受講コース",
    taikenbi: "無料体験授業のご希望日",
    taikenjikan: "無料体験授業のご希望時間帯",
    taikenbayso: "無料体験授業のご希望受講場所",
    gakkou: "生徒の学校",
  };
  const REGEX = {
    email:
      /^(([^<>()[\]\\.,;:一-龯ぁ-んァ-ン\s@"]+(\.[^<>()[\]\\.,;:一-龯ぁ-んァ-ン\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    katakana: /[ァ-ンｧ-ﾝﾞﾟ]$/,
    phone: /^\d{2,3}-\d{3,4}-\d{3,4}$|^\d{10,11}$/,
    zip_code: /^\d{3}-\d{4}$|^\d{7}$/,
  };

  $("#SelectClassroom").on("change", function () {
    if (this.value == "その他") {
      $("#ClassroomEtc").show();
    } else {
      $("input[name=class_room-etc]").val("");
      $("#ClassroomEtc").hide();
    }
  });

  $("#SelectContent").on("change", function () {
    switch (this.value) {
      case "その他":
        $("#ContentEtc").show();
        $("#ContentOption").hide();
        break;

      case "春期講習希望":
        $("#ContentOption").show();
        $("#ContentEtc").hide();
        break;

      default:
        $("input[name=content-etc]").val("");
        $("input[name=contentoption]").prop("checked", false);
        $("#ContentOption").hide();
        $("#ContentEtc").hide();
        break;
    }
  });

  // ページ離脱時にアラートを表示する
  function showUnloadMessage() {
    return ContactForm.unload_message;
  }

  // contact-form-nav クラスが付与されたリンクはアラートを出さない
  $(".contact-form-nav").on("click", function () {
    $(window).off("beforeunload", showUnloadMessage);
  });

  /**
   * リアルタイム入力値検証
   */
  $.each(Object.keys(ContactForm.forms), function () {
    var form_name = this;

    $('[name="' + form_name + '"]').on("blur", { form_name: form_name }, validateForm);
  });

  // -- 設定に応じてオプション機能を追加

  /**
   * 郵便番号で住所補完機能（ajaxzip3）
   *
   * data-contactform-zip="keyword" 郵便番号フィールドの指定
   * data-contactform-pref="keyword" 都道府県入力フィールドの指定
   * data-contactform-address="keyword" 住所入力フィールドの指定
   *
   * ※ keyword を同じにすることにより各フィールドの関連付けを行う
   * ※ 都道府県入力フィールドが存在しない場合には、住所入力フィールドに全ての住所を入力する
   */
  (function () {
    // フォームキーワードを抽出して複数フォームの住所補完に対応
    var keywords = [];

    $("input[data-contactform-zip]").each(function () {
      keywords.push($(this).data("oznformZip"));
    });

    if (keywords.length > 0) {
      $.each($.unique(keywords), function () {
        var keyword = this;

        var $zip_fields = $('input[data-contactform-zip="' + keyword + '"]');
        var pref_elem_name = $('input[data-contactform-pref="' + keyword + '"]').attr("name");
        var addr_elem_name = $('input[data-contactform-address="' + keyword + '"]').attr("name");

        if (!pref_elem_name) {
          pref_elem_name = addr_elem_name;
        }

        if ($zip_fields.length == 1) {
          $zip_fields.on("keyup", function () {
            AjaxZip3.zip2addr(this, "", pref_elem_name, addr_elem_name);
          });
        } else {
          $($zip_fields[1]).on("keyup", function () {
            AjaxZip3.zip2addr(
              $($zip_fields[0]).attr("name"),
              $($zip_fields[1]).attr("name"),
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

    $("input[data-autoruby]").each(function () {
      keywords.push($(this).data("autoruby"));

      $.each(keywords, function () {
        var keyword = this;

        var $hiragana_ruby = $('input[data-autoruby-hiragana="' + keyword + '"]');
        var $katakana_ruby = $('input[data-autoruby-katakana="' + keyword + '"]');

        if ($hiragana_ruby.length == 1) {
          $.fn.autoKana('input[data-autoruby="' + keyword + '"]', 'input[data-autoruby-hiragana="' + keyword + '"]');
        } else if (($katakana_ruby.length = 1)) {
          $.fn.autoKana('input[data-autoruby="' + keyword + '"]', 'input[data-autoruby-katakana="' + keyword + '"]', {
            katakana: true,
          });
        }
      });
    });
  })();

  /**
   * メールドメイン補完機能（domain_suggest.js）
   */
  (function () {
    $("[data-domain-suggest]").each(function () {
      var $target = $(this);
      var suggest_area_id = "contact-form-suggest-" + $target.data("domainSuggest");

      // suggest.js とのイベント競合を避けるため、リアルタイム検証処理を解除
      $target.off("blur", validateForm);

      // suggest.js の blur時のイベント処理に合わせて検証を実施
      // 選択後、文字挿入処理と同時ぐらいに検証イベントが発生するためちょっと遅らせて実行するようにした
      $target.on("SuggestJSBlurEvent", function () {
        setTimeout(function () {
          validateForm($target.attr("name"));
        }, 500);
      });

      // ブラウザの autocomplete 機能をOFF
      $target.attr("autocomplete", "off");

      // サジェストリスト用の要素を用意
      $target.after('<div id="' + suggest_area_id + '" class="contact-form-suggest" style="display:none;"></div>');

      new Suggest.Local(
        $target.get(0),
        suggest_area_id,
        window.ContactForm.suggestMailList,

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
   *  data-contactform-fileup="keyword" アップロードフォームを挿入したい要素
   *
   *  ※ keyword はアップロードフォームのNAME値を指定
   *
   */
  (function () {
    var index = 1;

    $("[data-contactform-fileup]").each(function () {
      var $el = $(this);
      var form_name = $el.data("oznformFileup");

      var file_form_id = "contactform-upform" + index;
      var file_btn_id = ContactForm.utilities.uploadButtonElementName(form_name);
      var uploaded_files_id = ContactForm.utilities.updatedFileElementName(form_name);

      // ファイルアップロードフォームのテンプレート
      var upload_form_template =
        "\n" +
        '<span id="' +
        file_btn_id +
        '" class="fileinput-button" data-formname="' +
        form_name +
        '">' +
        '<button type="button">添付ファイル追加</button>' +
        '<input id="' +
        file_form_id +
        '" class="fileupload" type="file" name="files[]" multiple>' +
        "</span>" +
        '<div id="' +
        uploaded_files_id +
        '" class="contactform-uploaded-files"></div>';
      $el.append(upload_form_template);

      var upload_options = {
        dropZone: $(),
        url: ContactForm.furl,
        dataType: "json",
        done: function (e, data) {
          $.each(data.result.files, function (index, file) {
            var $files_el = $("#" + uploaded_files_id);
            if (file.error) {
              alert(file.error);
            } else {
              ContactForm.utilities.addUploadFileElement(
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

      if (ContactForm.client_resize_config.enableClientResize) {
        upload_options.imageOrientation = true;
        upload_options.processQueue = [
          {
            action: "loadImageMetaData",
          },
          {
            action: "loadImage",
            fileTypes: /^image\/(gif|jpeg|jpg|JPG|png)$/,
            maxFileSize: 10000000, // 10MB
          },
          {
            action: "resizeImage",
            orientation: true,
            maxWidth: ContactForm.client_resize_config.maxWidth,
            maxHeight: ContactForm.client_resize_config.maxHeight,
          },
          {
            action: "saveImage",
          },
        ];
      }

      $("#" + file_form_id).fileupload(upload_options);

      index++;
    });
  })();

  // 送信リンクの連続クリック防止
  ContactForm.utilities.setSendmailButtonEvent($(".contact-form-send"));

  // エンターキー押下時の送信を無効化する
  ContactForm.utilities.disableEnterKeySubmit();

  // セッションに入っているデータをフォームに適用する
  ContactForm.utilities.setSessionData(ContactForm.page_data, ContactForm.forms);

  /**
   * getで渡された値をフォームに初期値として挿入
   *
   * @note
   *  フォームルートのみの適用。
   *  すでにフォームに値がある場合は値を挿入しない。
   *
   */
  ContactForm.utilities.setInitMessage(ContactForm.init_msg);

  /**
   * Datepickerを適用する
   */
  $("[data-of_datepicker]").each(function () {
    $(this).datepicker();
  });

  /**
   * フォーム検証処理
   * @param {string} form_name
   */
  function validateForm(event) {
    var form_name = event;

    if (typeof event != "string") {
      form_name = event.data.form_name;
    }

    if (ContactForm.forms[form_name]["validates"]) {
      validFormValue(form_name, ContactForm.forms[form_name]);
    } else {
      setVaildMark($(this));
    }

    let validations = [];

    $.each(Object.keys(ContactForm.forms), function () {
      var form_name = this;
      var form_config = ContactForm.forms[form_name];
      var $form_el = $('[name="' + form_name + '"]');

      if (!$form_el.hasClass("contact-form-valid") && ContactForm.forms[form_name]["validates"]) {
        validations.push(validFormValue(form_name, form_config, true));
      }
    });

    if (validations.length === 0) {
      $("#GroupButton p").hide();
      $("#GroupButton button").show();
    } else {
      $.when
        .apply($, validations)

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
            }
          });
          if (is_success) {
            $("#GroupButton p").hide();
            $("#GroupButton button").show();
          } else {
            $("#GroupButton p").show();
            $("#GroupButton button").hide();
          }
        });
    }

    if (!inputed) {
      // 離脱アラートを表示（送信時は解除するため関連実装あり）
      if (ContactForm.unload_message) {
        $(window).off("beforeunload", showUnloadMessage);
        $(window).on("beforeunload", showUnloadMessage);
      }

      inputed = true;
    }
  }

  // 送信時の入力値検証
  $("#SubmitForm").click(function () {
    const DataForm = $("#FormDataContact").serializeArray();
    let formData = new FormData();
    let contentOptionData = "";
    for (let index = 0; index < DataForm.length; index++) {
      const element = DataForm[index];
      if (element.name == "contentoption") {
        contentOptionData += element.value + ", ";
      } else {
        formData.append(element.name, element.value);
      }
    }

    formData.append("content_option", contentOptionData.slice(0, -2));
    formData.append("action", "save_contact_form");

    const url = "https://" + location.host + "/wp-admin/admin-ajax.php";

    $("#SubmitForm").prop("disabled", true);
    $.ajax({
      type: "POST",
      url: url,
      async: false,
      data: formData,
      dataType: "json",
      enctype: "multipart/form-data",
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.status == "success") {
          $(".form-confirm").hide();
          $(".form-thanks").show();
          $(".contact-form-stepbar li").removeClass("current");
          $(".contact-form-stepbar li:nth-child(3)").addClass("current");
          $(".text-note-confirm").hide();
          $("html, body").animate(
            {
              scrollTop: $(".form-thanks").offset().top - 200,
            },
            300,
          );

          (function () {
            function loadScriptRTCV(callback) {
              var script = document.createElement("script");
              script.type = "text/javascript";
              script.src = "https://www.rentracks.jp/js/itp/rt.track.js?t=" + new Date().getTime();
              if (script.readyState) {
                script.onreadystatechange = function () {
                  if (script.readyState === "loaded" || script.readyState === "complete") {
                    script.onreadystatechange = null;
                    callback();
                  }
                };
              } else {
                script.onload = function () {
                  callback();
                };
              }
              document.getElementsByTagName("head")[0].appendChild(script);
            }

            loadScriptRTCV(function () {
              _rt.sid = 6835;
              _rt.pid = 9761;
              _rt.price = 0;
              _rt.reward = -1;
              _rt.cname = "";
              _rt.ctel = "";
              _rt.cemail = "";
              _rt.cinfo = response.contact_id;
              rt_tracktag();
            });
          })(function () {});
        }
      },
      error: function (err) {
        console.log("error", err);
      },
      always: function (err) {
        $("#SubmitForm").prop("disabled", false);
      },
    });
  });
  $("#BackForm").click(function () {
    $(".form-input-data").show();
    $(".form-confirm").hide();
    $(".text-note-confirm").hide();
  });
  $("#NextPageConfirm").on("click", validateAllForms);

  /**
   * 全てのフォームを検証処理する
   *
   * @returns {boolean}
   */
  function validateAllForms() {
    const DataForm = $("#FormDataContact").serializeArray();
    var ajax_validations = [];

    $.each(Object.keys(ContactForm.forms), function () {
      var form_name = this;
      var form_config = ContactForm.forms[form_name];
      var $form_el = $('[name="' + form_name + '"]');
      var is_fileup_form = form_config.type === "upload_files";

      if ($form_el.length == 0 && !is_fileup_form) {
        return true;
      }

      if (!$form_el.hasClass("contact-form-valid") && ContactForm.forms[form_name]["validates"]) {
        ajax_validations.push(validFormValue(form_name, form_config));
      } else if (!ContactForm.forms[form_name]["validates"]) {
        if (!is_fileup_form) {
          setVaildMark($form_el);
        }
      }
    });

    if (ajax_validations.length === 0) {
      // -- 全て検証OKの時の処理

      $(".form-input-data").hide();
      $(".text-note-confirm").show();
      $(".form-confirm").show();
      $(".contact-form-stepbar li").removeClass("current");
      $(".contact-form-stepbar li:nth-child(2)").addClass("current");
      $("html, body").animate(
        {
          scrollTop: $(".form-confirm").offset().top - 200,
        },
        300,
      );

      let addressValue = "";
      let contentoptionData = "";
      for (const elm of DataForm) {
        if (elm.name == "contentoption") {
          contentoptionData += elm.value + ", ";
        } else {
          $(`#${elm.name}_value`).text(elm.name == "zip_code" && elm.value != "" ? `〒${elm.value}` : elm.value);

          if (["pref", "address", "address_building"].includes(elm.name)) {
            addressValue += elm.value;
          }
        }
      }
      $(`#contentoption_value`).text(contentoptionData.slice(0, -2));
      $(`#address_value`).text(addressValue);
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

            $(".form-input-data").hide();
            $(".text-note-confirm").show();
            $(".form-confirm").show();
            $(".contact-form-stepbar li").removeClass("current");
            $(".contact-form-stepbar li:nth-child(2)").addClass("current");
            $("html, body").animate(
              {
                scrollTop: $(".form-confirm").offset().top - 200,
              },
              300,
            );
            let addressValue = "";
            let contentoptionData = "";
            for (const elm of DataForm) {
              if (elm.name == "contentoption") {
                contentoptionData += elm.value + ", ";
              } else {
                $(`#${elm.name}_value`).text(elm.name == "zip_code" && elm.value != "" ? `〒${elm.value}` : elm.value);

                if (["pref", "address", "address_building"].includes(elm.name)) {
                  addressValue += elm.value;
                }
              }
            }
            $(`#contentoption_value`).text(contentoptionData.slice(0, -2));
            $(`#address_value`).text(addressValue);
            return false;
            // submitFormAfterCheckValidate();
          } else {
            // -- 検証NGの時の処理

            // 成功した要素を非表示にする
            hideValidItem();

            // 検証失敗したフォームまでスクロールバック
            var top_error_position = $(".contact-form-invalid").eq(0).offset().top;

            $("html,body").animate({
              scrollTop: top_error_position + ContactForm.vsetting.shift_scroll_position,
            });

            // 送信状態のボタンを元に戻す
            window.ContactForm.utilities.clearSendingButtonStyle($(".contact-form-send"));

            // 離脱アラートを設定する
            if (ContactForm.unload_message) {
              $(window).off("beforeunload", showUnloadMessage);
              $(window).on("beforeunload", showUnloadMessage);
            }
          }
        })
        .fail(function () {
          alert("通信に失敗しました。");
          return false;
        });
    }

    return false;
  }

  /**
   * フォーム入力値検証
   * @param form_name
   * @param form_config
   */
  function validFormValue(form_name, form_config, auto_check = false) {
    var dInner = new $.Deferred();
    var t = [form_name];
    var $form_el = $('[name="' + form_name + '"]');
    var validate_condition = form_config.validate_condition;
    var post_condition = {};

    if (form_config.type === "upload_files") {
      $form_el = $("#" + ContactForm.utilities.uploadButtonElementName(form_name));
    }

    if (validate_condition) {
      t = t.concat(window.ContactForm.utilities.objectKeys(validate_condition));

      $.each(validate_condition, function (key, value) {
        post_condition[key.replace("[]", "")] = value;
      });
    }

    var form_values = window.ContactForm.utilities.getFormValues(t, true);

    form_name = form_name.replace("[]", "");

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

    if (!auto_check) clearInvalidMessages(form_name);

    var response = checkValidate(post_data);

    // 検証OKのときの処理
    if (response.valid) {
      if (!auto_check) {
        if (response.warning) {
          setWarningMark($form_el, response.warning, form_config);
        } else {
          setVaildMark($form_el);
        }
      }

      dInner.resolveWith(true);

      // 検証NGのときの処理
    } else {
      if (!auto_check) {
        setInvalidMark($form_el, response.errors[form_name], form_config);
      }
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
      if (valid == "required" && !value) {
        validateJSON.valid = false;
        validateJSON.errors[name] = [`${elmLabelError}を入力してください`];
        break;
      }

      // check email
      if (valid == "email_detail" && value) {
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
      if (valid == "kanaOnly" && value) {
        if (!REGEX.katakana.test(value)) {
          validateJSON.valid = false;
          validateJSON.errors[name] = [`${elmLabelError}は「ひらがな」か「カタカナ」で入力してください`];
          break;
        }
      }

      // phone number
      if (valid == "tel" && value) {
        if (!REGEX.phone.test(value)) {
          validateJSON.valid = false;
          validateJSON.errors[name] = [
            `${elmLabelError}は市外局番を含む数字またはハイフンの組み合わせで入力してください`,
          ];
          break;
        }
      }

      // zip code
      if (valid == "zip_code" && value) {
        if (!REGEX.zip_code.test(value)) {
          validateJSON.valid = false;
          validateJSON.errors[name] = [`7桁の郵便番号を入力してください`];
          break;
        }
      }

      // survey
      if (valid == "survey" && value) {
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
    $("." + form_name + ".contact-form-errors").remove();
    $("." + form_name + ".contact-form-warning").remove();
  }

  /**
   * フォームを検証OKの表示にする
   * @param $form_el
   */
  function setVaildMark($form_el) {
    addResultClass($form_el, true);
    // appendResultIcon($form_el, true);
  }

  /**
   * フォームを検証OKの表示にして、注意メッセージを表示する
   * @param $form_el
   * @param warning_message
   * @param form_config
   */
  function setWarningMark($form_el, warning_message, form_config) {
    addResultClass($form_el, true);
    appendErrorMessages($form_el, warning_message, form_config, true);
    // appendResultIcon($form_el, true);
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
    appendErrorMessages($form_el, error_message, form_config, false);
    // appendResultIcon($form_el, false);
  }

  /**
   * 検証結果に応じて検証結果判定クラスを付与する
   *
   * @note
   *   contact-form-valid   : 検証OK
   *   contact-form-invalid : 検証NG
   *
   * @param $el
   * @param is_valid
   */
  function addResultClass($el, is_valid) {
    // 対象要素がチェックボックスやラジオボタンの時は、ozn-check要素にクラスを追加する
    if ($.inArray($el.attr("type"), ["checkbox", "radio"]) >= 0) {
      var $ozn_check = $el.closest(".contact-check");
      if ($ozn_check.length > 0) {
        $el = $ozn_check;
      }
    }

    if (is_valid) {
      $el.removeClass("contact-form-invalid").addClass("contact-form-valid");
    } else {
      $el.removeClass("contact-form-valid").addClass("contact-form-invalid");
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
  function appendErrorMessages($el, msg, form_config, warning) {
    var form_name = ContactForm.utilities.getFormNameByElement($el);
    var template = $("<div>" + msg.join("<br />") + "</div>");

    // エラー位置の指定があれば基準要素を置換
    if (form_config.error_message_position) {
      $el = $(form_config.error_message_position);

      // 対象要素がチェックボックスやラジオボタンの時は、ozn-checkをデフォルトとする
    } else if ($.inArray($el.attr("type"), ["checkbox", "radio"]) >= 0) {
      var $ozn_check = $el.closest(".contact-check");
      if ($ozn_check.length > 0) {
        $el = $ozn_check;
      }
    }

    // エラーメッセージテンプレートがあればデフォルトテンプレートを置換
    if (form_config.error_message_template) {
      template = $(form_config.error_message_template.replace("<% messages %>", msg.join("<br />")));
    }

    // ドメインサジェスト表示エリアを取得
    var $suggest_area = $el.siblings(".contact-form-suggest");

    // 付加するクラスを指定
    var added_class = "contact-form-errors";
    if (warning) {
      added_class = "contact-form-warning";
    }

    if ($suggest_area.length > 0) {
      $suggest_area.after(template.addClass(form_name.replace("[]", "") + " " + added_class));
    } else {
      $el.after(template.addClass(form_name.replace("[]", "") + " " + added_class));
    }
  }

  /**
   * 検証結果に応じたアイコン要素を挿入する
   *
   * @param {jQuery}  $el
   * @param {boolean} is_valid
   * @returns {number}
   */
  function appendResultIcon($el, is_valid) {
    var form_name = ContactForm.utilities.getFormNameByElement($el);

    // 既存アイコンを初期化
    $("." + form_name.replace("[]", "") + ".contact-form-icon").remove();

    if (!ContactForm.vsetting.show_icon) {
      return 1;
    }

    // 対象要素がチェックボックスやラジオボタンの時は、ozn-check要素の後ろにアイコン追加する
    if ($.inArray($el.attr("type"), ["checkbox", "radio"]) >= 0) {
      var $ozn_check = $el.closest(".contact-check");
      if ($ozn_check.length > 0) {
        $el = $ozn_check;
      }
    }
  }

  /**
   * 検証OKの項目を非表示にする
   */
  function hideValidItem() {
    $("[data-contactform-area]").each(function () {
      var $this = $(this);
      var form_name = $this.data("oznformArea");
      var $form = $('[name="' + form_name + '"]');

      if ($form.hasClass("contact-form-valid") || $form.closest(".contact-check").hasClass("contact-form-valid")) {
        // $this.hide();
      }
    });
  }
});
