<template>
    <div class="form-group">
        <label v-if="`resume` === name">R&eacute;sum&eacute;</label>
        <input type="file" class="form-control" :name="name" :accept="acceptedFileTypes" @change="fileChange" ref="input">
        <small class="form-text text-right" :required="required">
            <a href="#" class="text-danger" @click="removeFile">(Remove)</a>
        </small>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>