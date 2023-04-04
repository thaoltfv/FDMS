import Vue from 'vue'
import VueI18n from 'vue-i18n'
import validationMessagesEnglish from 'vee-validate/dist/locale/en'
import validationMessagesJapanese from 'vee-validate/dist/locale/ja'
import validationMessagesVietnamese from 'vee-validate/dist/locale/vi'
// import languages
import english from '../../languages/en'
import japanese from '../../languages/ja'
import vietnamese from '../../languages/vi'
import { LOCALE } from '../../enum/locale.enum'

Vue.use(VueI18n)

const dictionary = {
	en: {
		format_tel: 'The {_field_} is wrong format',
		decimal: 'The {_field_} is wrong format',
		half_alphabet: 'The {_field_} is wrong format',
		youtube_valid: 'The {_field_} is wrong format',
		greaterThanZero: 'The {_field_} is wrong format'
	},
	ja: {
		format_tel: '不正な形式になっています。',
		decimal: '不正な形式になっています。',
		half_alphabet: '不正な形式になっています。',
		youtube_valid: '不正な形式になっています。',
		only_kata: 'カタカナのみ入力してください。',
		not_emoji: '使用できない文字が入力されています。',
		greaterThanZero: '{_field_}は必須項目です'
	}
}

const messages = {
	english: { ...english, validation: { ...validationMessagesEnglish.messages, ...dictionary.en } },
	japanese: { ...japanese, validation: { ...validationMessagesJapanese.messages, ...dictionary.ja } },
	vietnamese: { ...vietnamese, validation: { ...validationMessagesVietnamese.messages, ...dictionary.vi } }
}

const i18n = new VueI18n({
	locale: LOCALE.vi, // set locale
	messages,
	fallbackLocale: LOCALE.vi,
	silentTranslationWarn: true
})

export default i18n
