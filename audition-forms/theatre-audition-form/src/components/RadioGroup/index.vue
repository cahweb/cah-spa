<template>
    <div class="radio-group">
        <template v-for="(button, i) in buttons">
            <div :key="i" class="form-check" :class="inline ? 'form-check-inline' : null">
                <label :for="`${name}-${i}`" class="form-check-label btn btn-primary btn-sm mx-2">
                    <input type="radio" class="form-check-input" :name="name" :id="`${name}-${i}`" v-model="value" :value="button" @click="update(`${name}-${i}`)">
                    {{ button }}
                </label>
            </div>
        </template>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>