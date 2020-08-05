<template>
    <div class="app">
        <form v-show="!isSubmitted" id="music-audition-form" method="post" action="" @submit="submitForm">
            <program-select :levels="buttons.level" 
                :programs="buttons.programs"
                :base-url="baseUrl"
            />
            <student-info v-show="showAuditionInfo"
                :inputs="fields.studentInfo"
            />
            <audition-info v-show="showAuditionInfo" 
                :buttonList="buttons.years" 
                :fieldList="fields.auditionInfo" 
            />
            <recaptcha v-show="showAuditionInfo" :site-key="rcSiteKey" :lang="lang" />
            <button v-show="showAuditionInfo" id="submitButton" type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div v-if="isSubmitted" class="status-box" id="status">
            <p>{{ displayMessage }}</p>
        </div>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>