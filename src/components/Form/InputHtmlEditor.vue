<template>
  <ValidationProvider :name="label"
                      :rules="rules"
                      :vid="vid"
                      v-slot="{ errors, required }">
    <label class="form-label text-capitalize d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-start font-weight-bold">
        {{ $t(`${label}`) }}
        <span v-if="required" class="required">{{$t('required')}}</span>
      </div>
      <span v-if="rules.includes('max:')" class="character-count">
        {{value.length}}/{{maxLength}}
      </span>
    </label>
    <div class="editor_note">
      {{$t('editor_counter_note')}}
    </div>
    <div class="form-control-plaintext">
      <vue-editor v-model="editorValue"
                  :class="errors.length ? 'is-invalid' : ''"
                  id="quill-editor"
                  class="d-block editor"
                  ref="editor"
                  :editorToolbar="customToolbar">
      </vue-editor>

      <span class="error invalid-feedback" v-if="errors.length">{{ errors[0] }}</span>
    </div>

    <template>
      <a-modal v-model="htmlVisible"
               id="files-popup"
               :title="$t('content')"
               :on-ok="handleOk"
               :width="'70vw'"
               :zIndex="1100"
               :keyboard="false">
        <div class="row m-2">
          <label class="form-label text-capitalize d-flex align-items-center mb-2">{{ $t('html_editor') }}</label>
          <textarea style="min-height: 40vh"
                    class="form-control"
                    :max-length="maxLength"
                    v-model="htmlContent"></textarea>
        </div>
        <template slot="footer">
          <button type="button" class="btn btn-secondary" @click="htmlVisible = false">
            <i class="fe fe-slash"/>

            {{ $t('cancel') }}
          </button>

          <button type="button" class="btn btn-primary" @click="handleOk">
            <i class="fe fe-check-circle"/>
            {{ $t('ok') }}
          </button>
        </template>
      </a-modal>
    </template>
  </ValidationProvider>
</template>

<script>
import {VueEditor} from 'vue2-editor'

export default {
	name: 'InputHtmlEditor',

	components: {
		VueEditor
	},

	model: {
		prop: 'value',
		event: 'change'
	},

	props: {
		placeholder: {
			type: String,
			default: ''
		},
		label: {
			type: String,
			default: ''
		},
		rules: {
			type: String,
			default: ''
		},
		vid: {
			type: String,
			default: ''
		},
		required: {
			type: Boolean,
			default: false
		},
		maxLength: {
			type: [String, Number]
		},
		value: {
			type: String,
			required: true,
			default: ''
		},
		fileType: {
			type: String,
			default: null
		}
	},

	data () {
		return {
			htmlVisible: false,
			htmlContent: '',
			customToolbar: [
				[
					'bold',
					'italic',
					'underline',
					'strike'
				],
				['blockquote', 'code-block'],
				[{'header': 1}, {'header': 2}],
				[{'list': 'ordered'}, {'list': 'bullet'}],
				[{'script': 'sub'}, {'script': 'super'}],
				[{'indent': '-1'}, {'indent': '+1'}],
				[{'size': ['small', false, 'large', 'huge']}],
				[{'header': [false, 1, 2, 3, 4, 5, 6]}],
				[{'color': []}, {'background': []}],
				[{'align': []}],
				(this.$route.name === 'coupons.create' || this.$route.name === 'coupons.edit') ? [] : ['image'],
				['custom-html-' + this._uid],
				['clean']
			]
		}
	},

	mounted () {
		let toolbarTooltips = {
			'font': 'フォント選択',
			'size': 'フォントサイズ',
			'header': 'ヘッダーレベル',
			'bold': '太字',
			'italic': '斜体',
			'underline': '下線',
			'strike': '取り消し',
			'blockquote': '引用',
			'code-block': 'コード',
			'color': 'フォントカラー',
			'background': '背景色',
			'script': {
				'sub': '下付き',
				'super': '上付き'
			},
			'list': {
				'ordered': '箇条書き（数字）',
				'bullet': '箇条書き'
			},
			'indent': {
				'-1': 'インデントを減らす',
				'+1': 'インデントを増やす'
			},
			'direction': {
				'rtl': 'Text direction (right to left | left to right)',
				'ltr': 'Text direction (left ro right | right to left)'
			},
			'align': '配置',
			'link': 'Insert a link',
			'image': 'Insert an image',
			'formula': 'Insert a formula',
			'clean': '書式の削除',
			'add-table': 'Add a new table',
			'table-row': 'Add a row to the selected table',
			'table-column': 'Add a column to the selected table',
			'remove-table': 'Remove selected table',
			'help': 'Show help'
		}
		toolbarTooltips[`custom-image-${this._uid}`] = '画像'
		toolbarTooltips[`custom-html-${this._uid}`] = 'HTMLソース'

		let showTooltip = (which, el) => {
			let tool

			if (which === 'button') {
				tool = el.className.replace('ql-', '')
			} else if (which === 'span') {
				tool = el.className.replace('ql-', '')
				tool = tool.substr(0, tool.indexOf(' '))
			}

			// create element tooltip
			let tooltipCustom = document.createElement('b')
			// add class for tooltip
			tooltipCustom.className = 'tooltip-custom'
			// append to button
			el.append(tooltipCustom)

			if (tool) {
				// if element has value attribute.. handling is different
				// buttons without value
				if (el.value === '') {
					if (toolbarTooltips[tool]) {
						el.querySelector('.tooltip-custom').innerHTML = toolbarTooltips[tool]
					}
					// eslint-disable-next-line brace-style
				}
				// buttons with value
				else if (typeof el.value !== 'undefined') {
					if (toolbarTooltips[tool][el.value]) { el.querySelector('.tooltip-custom').innerHTML = toolbarTooltips[tool][el.value] }
					if (el.getAttribute('value') === '1') { el.querySelector('.tooltip-custom').innerHTML = 'ヘッダー1' }
					if (el.getAttribute('value') === '2') { el.querySelector('.tooltip-custom').innerHTML = 'ヘッダー2' }
					// eslint-disable-next-line brace-style
				}
				// default
				else {
					el.querySelector('.tooltip-custom').innerHTML = toolbarTooltips[tool]
				}
			}
		}

		let toolbarElement = document.querySelector('.ql-toolbar')
		if (toolbarElement) {
			let matchesButtons = toolbarElement.querySelectorAll('button')
			for (let el of matchesButtons) {
				showTooltip('button', el)
			}
			// for submenus inside
			let matchesSpans = toolbarElement.querySelectorAll('.ql-toolbar > span > span')
			for (let el of matchesSpans) {
				showTooltip('span', el)
			}
			// for picker option :not(.ql-color-picker) > .ql-picker-options > .ql-picker-item
			// let matchesSpansOption = toolbarElement.querySelectorAll('.ql-align .ql-picker-options > span')
			// for (let el of matchesSpansOption) {
			//   el.classList.add('position-relative')
			//   console.log('option', el)
			//   showTooltip('span', el)
			// }
		}

		// QUILL IMAGE
		// const customButton = document.querySelector('.ql-custom-image-' + this._uid)
		// customButton.addEventListener('click', this.showSelectImage)

		const htmlButton = document.querySelector('.ql-custom-html-' + this._uid)
		htmlButton.addEventListener('click', this.showHtml)
	},

	computed: {
		quill () {
			return this.$refs.editor.quill
		},
		editorValue: {
			get () {
				return this.value
			},
			set
			(val) {
				this.$emit('change', val)
			}
		}
	},

	methods: {
		showHtml () {
			this.htmlContent = this.editorValue
			this.htmlVisible = true
		},
		showSelectImage () {
			this.$refs.selectImage.showModal()
		},
		handleOk () {
			this.editorValue = this.htmlContent
			this.htmlVisible = false
		},
		onFileSelect (file, keyName) {
			const range = this.quill.selection.savedRange
			this.quill.insertEmbed(range.index, 'image', file.url)
		}
	}

}
</script>

<style lang="scss" scoped>
  .character-count {
    font-size: 12px;
    font-weight: normal;
    color: #999999;
  }

  .editor_note {
    margin-bottom: 10px;
  }

  .editor {
    /deep/.ql-toolbar {
      button {
        outline: none;
        position: relative;
        .tooltip-custom {
          position: absolute;
          display: block;
          background: #ffffff;
          border: 1px solid #333;
          top: calc(100% + 10px);
          left: 20px;
          opacity: 0;
          visibility: hidden;
          white-space: nowrap;
          padding: 1px 2px;
          font-weight: 500;
          color: #333333;
          font-size: 12px;
          z-index: 2;
        }
        &:hover {
          & >.tooltip-custom {
            opacity: 1;
            visibility: visible;
          }
        }
      }
      span {
        span {
          .tooltip-custom {
            position: absolute;
            display: block;
            background: #ffffff;
            border: 1px solid #333;
            top: calc(100% + 10px);
            left: 20px;
            opacity: 0;
            visibility: hidden;
            white-space: nowrap;
            padding: 1px 2px;
            font-weight: 500;
            color: #333333;
            font-size: 12px;
          }
          &:hover {
            & >.tooltip-custom {
              opacity: 1;
              visibility: visible;
            }
          }
        }
      }
    }
  }
</style>
