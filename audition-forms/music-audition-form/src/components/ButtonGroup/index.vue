<template>
    <div class="radio-group col-12 mb-3">
        <div v-for="(button, i) of buttonList" :key="i" class="form-check mx-2" :class="{'form-check-inline': inline}">
            <label class="form-check-label btn btn-primary btn-sm">
                <input :type="button.type || defaultType" :id="`${name}-i`" :name="`${name}${button.type === 'checkbox' ? `[]` : ''}`" class="form-check-input" :value="button.value" v-model="value" @change="buttonClick">
                {{ button.text }}
            </label>
        </div>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>