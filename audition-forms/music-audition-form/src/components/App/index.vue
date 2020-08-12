<template>
    <div class="app">
        <form v-show="!isSubmitted" id="music-audition-form" method="post" action="" @submit="submitForm">
            <div class="row mb-3" v-if="values.program.hasForm === false">
                <div class="col-12">
                    <p>You are reveiwing instructions to apply for the <strong>{{ programName }}</strong> program. Please be sure you have selected the correct program before continuing.</p>
                </div>
            </div>
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
            <div class="row mb-3" v-if="values.program.hasForm">
                <div class="form-check col-12">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="programAcknowledge" value="1" required>
                        You are submitting an application to the UCF School of Performing Arts for the <strong>{{ programName }}</strong> program. Please click to confirm that this is the correct degree program before continuing.
                    </label>
                </div>
            </div>
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