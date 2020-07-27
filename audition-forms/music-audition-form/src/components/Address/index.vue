<template>
    <div class="address" :class="`col-${totalWidth !== undefined ? `md-${totalWidth}` : '12'}`">
        <div class="row">
            <text-input v-for="(field, i) in fields" :key="i"
                :colWidth="field.colWidth"
                :type="field.type !== undefined ? field.type : null"
                :label="field.label"
                :name="field.name"
                :maxLength="field.maxLength"
                :emitOn="'addressChange'"
                :isRequired="field.required"
            />
        </div>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>