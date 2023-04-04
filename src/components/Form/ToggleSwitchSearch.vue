<template>
  <div class="container__toggle">
    <div class="toggle-button" :id="'toggleButton' + group" @click="onButtonToggle(options.items.labels[1].name)"></div>
    <div class="toggle-position" :id="'toggleLeft' + group" @click="onButtonToggle(options.items.labels[0].name)"></div>
    <div class="toggle-position" :id="'toggleCenter' + group" @click="onButtonToggle(options.items.labels[1].name)"></div>
    <div class="toggle-position" :id="'toggleRight' + group" @click="onButtonToggle(options.items.labels[2].name)"></div>
  </div>
</template>

<script>
const s = x => x + 's'
const rem = v => v + 'rem'

export default {
	name: 'ToggleSwitch',
	props: {
		options: {
			type: Object,
			required: false
		},
		value: {
			type: String,
			required: false
		},
		name: {
			type: String,
			required: false
		},
		group: {
			type: String,
			required: false,
			default: ''
		},
		disabled: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	created () {
		this.defaultOptions = {
			layout: {
				color: 'black',
				backgroundColor: 'transparent',
				selectedColor: 'white',
				selectedBackgroundColor: 'green',
				borderColor: 'gray',
				fontFamily: 'Arial',
				fontWeight: 'normal',
				fontWeightSelected: 'normal',
				squareCorners: false,
				noBorder: false
			},
			size: {
				fontSize: 1.5,
				height: 3.25,
				padding: 0.5,
				width: 20
			},
			items: {
				delay: 0.4,
				preSelected: 'neutral',
				disabled: false,
				labels: [
					{name: 'Off', color: 'white', backgroundColor: 'red'},
					{name: 'unknown', color: 'white', backgroundColor: 'grey'},
					{name: 'On', color: 'white', backgroundColor: 'green'}
				]
			}
		}
	},
	mounted () {
		const toggleButton = document.getElementById('toggleButton' + this.group)
		switch (this.options.items.preSelected) {
		case this.options.items.labels[0].name:
			this.activeOption = this.options.items.labels[2].name
			toggleButton.style.left = 0
			break
		case this.options.items.labels[2].name:
			this.activeOption = this.options.items.labels[0].name
			toggleButton.style.left = '100%'
			break
		default:
			toggleButton.style.left = '50%'
			break
		}
	},
	data () {
		return {
			selected: false,
			selectedItem: 'unknown',
			defaultOptions: Object,
			activeOption: undefined
		}
	},
	computed: {
		toggleSwitchStyle () {
			return {
				width: rem(this.defaultOptions.size.width),
				height: rem(this.defaultOptions.size.height)
			}
		},
		itemStyle () {
			return {
				width: rem(this.defaultOptions.size.width),
				height: rem(this.defaultOptions.size.height),
				fontFamily: this.defaultOptions.layout.fontFamily,
				fontSize: rem(this.defaultOptions.size.fontSize),
				textAlign: 'center'
			}
		},
		labelStyle () {
			return {
				padding: rem(this.defaultOptions.size.padding),
				borderColor: this.defaultOptions.layout.noBorder ? 'transparent' : this.defaultOptions.layout.borderColor,
				backgroundColor: this.defaultOptions.layout.backgroundColor,
				color: this.defaultOptions.layout.color,
				fontWeight: this.defaultOptions.layout.fontWeight,
				transition: s(this.defaultOptions.items.delay)
			}
		}
	},
	methods: {
		toggle (event) {
			this.$emit('change', {
				value: event
			})
		},
		labelStyleSelected (color, backgroundColor) {
			return {
				padding: rem(this.defaultOptions.size.padding),
				borderColor: this.defaultOptions.layout.noBorder ? 'transparent' : this.defaultOptions.layout.borderColor,
				fontWeight: this.defaultOptions.layout.fontWeightSelected,
				backgroundColor: backgroundColor !== undefined ? backgroundColor : this.defaultOptions.layout.selectedBackgroundColor,
				color: color !== undefined ? color : this.defaultOptions.layout.selectedColor,
				transition: s(this.defaultOptions.items.delay)
			}
		},
		mergeDefaultOptionsWithProp (options) {
			var result = this.defaultOptions
			for (var option in options) {
				if (options[option] !== null && typeof (options[option]) === 'object') {
					for (var subOption in options[option]) {
						if (options[option][subOption] !== undefined && options[option][subOption] !== null) {
							result[option][subOption] = options[option][subOption]
						}
					}
				} else {
					result[option] = options[option]
				}
			}
		},

		onButtonToggle (activeOption) {
			const toggleButton = document.getElementById('toggleButton' + this.group)
			if (!this.disabled) {
				switch (activeOption) {
				case this.options.items.labels[0].name:
					toggleButton.style.left = 0
					this.activeOption = this.options.items.labels[0].name
					this.toggle(this.options.items.labels[0].name)
					break
				case this.options.items.labels[1].name:
					if (this.activeOption === this.options.items.labels[0].name) {
						toggleButton.style.left = '50%'
						this.activeOption = this.options.items.labels[1].name
						this.toggle(this.options.items.labels[1].name)
					} else {
						toggleButton.style.left = 0
						this.activeOption = this.options.items.labels[0].name
						this.toggle(this.options.items.labels[0].name)
					}
					break
				case this.options.items.labels[2].name:
					toggleButton.style.left = '100%'
					this.activeOption = this.options.items.labels[0].name
					this.toggle(this.options.items.labels[2].name)
					break
				default:
					toggleButton.style.left = 0
					this.activeOption = this.options.items.labels[1].name
					this.toggle(this.options.items.labels[0].name)
					break
				}
			}
		}
	},
	watch: {
		value (val) {
			this.selectedItem = val
		},
		options (val) {
			if (val !== null && val !== undefined) {
				this.mergeDefaultOptionsWithProp(val)
			}
		}
	}
}
</script>

<style lang="scss" scoped>

.container {
  &__toggle {
    height: 30px;
    width: 100px;
    border-radius: 30px;
    background: white;
    border: 1px solid #ff922b;
    position: relative;
    margin: 0 1rem;
    display: flex;
    justify-content: space-between;
  }
}

.toggle-button {
  height: 40px;
  width: 40px;
  background: #ff922b;
  border-radius: 50%;
  position: absolute;
  top: 50%;
  left: 50%;
  transform-origin: center center;
  transition: 0.3s;
  transform: translateY(-50%) translateX(-50%);
  cursor: pointer;
}

.toggle-position {
  cursor: pointer;
  width: calc(100%/3);
  height: 100%;
}
</style>
