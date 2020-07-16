<template>
    <div class="row form-section">
        <template v-for="(field, i) in inputs">
            <text-input v-if="this.name !== 'address'" :key="i" :label="input.label" :name="input.name" :colWidth="input.colWidth" :type="input.type !== undefined ? input.type : null" :val="input.val !== undefined ? input.val : null" :maxLength="input.maxLength !== undefined ? input.maxLength : null" />
        </template>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>