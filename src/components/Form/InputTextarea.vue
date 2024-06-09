<template>
	<ValidationProvider
		tag="div"
		:name="label ? label : nonLabel"
		:vid="vid"
		:rules="rules"
		v-slot="{ errors }"
	>
		<label
			class="form-label d-flex justify-content-between align-items-center font-weight-bold color_content"
		>
			<div>
				{{ $t(label) }} <b v-if="requiredIcon" class="ml-1 text-red">*</b>
				<span v-if="required" class="required">{{ $t("required") }}</span>
			</div>
			<span v-if="rules.includes('max:')" class="character-count">
				{{ value.length }}/{{ maxLength }}
			</span>
		</label>

		<div :class="{ has__error: errors[0] }">
			<a-textarea
				:rows="rows"
				:placeholder="placeholder"
				:max-length="maxLength"
				:value="value"
				:disabled="disableInput"
				:autoSize="autosize"
				@input="$emit('change', $event.target.value)"
				class="color_content"
			/>
			<!--Message Error-->
			<span v-if="errors[0]" class="errors">{{ errors[0] }}</span>
		</div>
	</ValidationProvider>
</template>

<script>
export default {
	name: "InputTextarea",

	model: {
		prop: "value",
		event: "change"
	},

	props: {
		requiredIcon: {
			type: Boolean,
			default: false
		},
		label: {
			type: String,
			default: ""
		},

		rules: {
			type: String,
			default: ""
		},

		vid: {
			type: String,
			default: ""
		},

		placeholder: {
			type: String,
			default: ""
		},

		maxLength: {
			type: [String, Number]
		},

		rows: {
			type: [String, Number]
		},

		value: {
			type: [String, Number],
			required: false,
			default: ""
		},

		required: {
			type: Boolean,
			default: false
		},
		autosize: {
			type: Boolean,
			default: false
		},
		disableInput: {
			type: Boolean,
			default: false
		},
		nonLabel: {
			type: String,
			default: ""
		}
	}
};
</script>

<style scoped>
.character-count {
	font-size: 12px;
	font-weight: normal;
	color: #999999;
}
</style>
