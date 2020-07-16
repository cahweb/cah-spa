<template>
    <div class="app">
        <div v-if="!isSubmitted" class="form-container">
            <form id="theatre-audition-form" method="post" enctype="multipart/form-data" ref="form">
                <p>Are you auditioning for an Undergraduate (B.A. or B.F.A.) or Graduate (M.A. or M.F.A.) program?</p>
                <div class="radio-group mb-3">
                    <template v-for="(button, i) in buttons.level">
                        <div :key="i" class="form-check form-check-inline mx-2">
                            <label class="form-check-label btn btn-primary btn-sm">
                                <input type="radio" :id="`level-${i}`" name="level" class="form-check-input" v-model="values.level" :value="button.value" @click="updateActive('level')">
                                {{ button.label }}
                            </label>
                        </div>
                    </template>
                </div>
                <div v-if="values.level !== ''" class="mb-5">
                    <p>Which program are you applying for?</p>
                    <div class="radio-group mb-3">
                        <template v-for="(button, i) in (values.level == 'undergrad' ? buttons.programs.undergrad : buttons.programs.grad)">
                            <div :key="i" class="form-check form-check-inline mx-2">
                                <label class="form-check-label btn btn-primary btn-sm">
                                    <input type="radio" :id="`program-${i}`" name="program" class="form-check-input" v-model="values.program" :value="button" @click="updateActive('program')">
                                    {{ button.label }}
                                </label>
                            </div>
                        </template>
                    </div>
                </div>
                <div v-if="showReqs" class="mb-5">
                    <h2 class="heading-underline">Program Requirements for {{ values.program.label }}</h2>
                    <div id="prog-reqs-preamble" class="mb-3">
                        <p id="prog-coord"><strong>Program Coordinator:</strong> {{ values.program.name }} (<a :href="`mailto:${values.program.email}`">{{ values.program.email }}</a>)</p>
                        <p v-if="values.level === 'undergrad'" id="undergrad-blurb">Students must apply and be accepted through both the UCF School of Performing Arts and <a href="https://www.ucf.edu/admissions/undergraduate">UCF Undergraduate Admissions</a>. It is encouraged to apply to both UCF Undergraduate Admissions and the UCF School of Performing Arts as early as possible. The school can provisionally accept students prior to being accepted by UCF Undergraduate Admissions but cannot offer official acceptance of study until students receive their UCF acceptance.</p>
                    </div>
                    <div v-html="programReqs" class="mb-3"></div>
                    <div id="prog-reqs-postamble">
                        <p><em>We don't want application fees to be a barrier. If you are submitting an application through AcceptD and need assistance, contact the coordinator for the program you are applying to for more information. For information about application fee waivers for UCF admissions, <a href="https://www.ucf.edu/admissions/undergraduate/question/how-can-i-request-a-waiver-of-the-application-fee/" target="_blank" rel="noopener">click here</a>.</em></p>
                    </div>
                </div>
                <div v-if="isBfaActing" class="mb-3">
                    <p class="mt-4">Did you want to schedule a live audition or submit a video audition?</p>
                    <div class="radio-group">
                        <template v-for="(button, i) in buttons.bfaActingChoice">
                            <div :key="i" class="form-check form-check-inline mx-2">
                                <label class="form-check-label btn btn-primary btn-sm">
                                    <input type="radio" :id="`bfa-acting-choice-${i}`" name="actingChoice" class="form-check-input" v-model="values.actingChoice" :value="button.value" @click="updateActive('actingChoice')">
                                    {{ button.label }}
                                </label>
                            </div>
                        </template>
                    </div>
                </div>
                <div v-show="showForm" class="mb-3">
                    <h4>Student Information</h4>
                    <div class="row w-75">
                        <template v-for="(input, i) in inputs.studentInfo">
                            <div :key="i" class="form-group col-md-6">
                                <label>{{ input.label }}:</label>
                                <input v-if="input.name !== 'pronouns'" :type="input.type !== undefined ? input.type : 'text'" :name="input.name" :maxlength="input.maxlength !== undefined ? input.maxlength : null" v-model="values.studentInfo[input.name]" class="form-control" :required="input.required">
                                <select v-else class="form-control" :name="input.name" v-model="values.studentInfo.pronouns">
                                    <option value=""> -- Please Select -- </option>
                                    <option v-for="(option, j) in input.options" :key="j" :value="option.value">
                                        {{ option.text }}
                                    </option>
                                </select>
                            </div>
                        </template>
                        <div v-if="values.studentInfo.pronouns === 'other'" class="form-group col-md-6">
                            <label>If Other, please specify:</label>
                            <input type="text" name="pronounOther" v-model="values.studentInfo.pronounOther" class="form-control">
                        </div>
                    </div>
                    <div class="row w-75 mb-3">
                        <template v-for="(input, i) in inputs.address">
                            <div :key="i" class="form-group" :class="`col-md-${input.colWidth !== undefined ? input.colWidth : 6}`">
                                <label>{{ input.label }}:</label>
                                <input :type="input.type !== undefined ? input.type : 'text'" :name="input.name" :maxlength="input.maxlength !== undefined ? input.maxlength : null" v-model="values.studentInfo.address[input.name]" class="form-control">
                            </div>
                        </template>
                    </div>
                    <h4>Select an Interview Date</h4>
                    <div class="row w-75 mb-3">
                        <template v-for="(ordinal, i) in ['First', 'Second']">
                            <div :key="i" class="form-group col-md-7" :class="{'has-danger': sameDate}">
                                <label>{{ ordinal }} Choice Date:</label>
                                <select :name="`audition${i + 1}`" v-model="values.auditionDates[ordinal.toLowerCase()]" class="form-control" :class="{'form-control-danger': sameDate}">
                                    <option value="">-- Please Select --</option>
                                    <option v-for="(date, j) in dateList" :key="j" :value="date.value">{{ date.text }}</option>
                                    <option value="next-available">Next Available Date</option>
                                </select>
                                <div v-if="ordinal === 'Second' && sameDate" class="form-control-feedback">Can't select the same date.</div>
                            </div>
                        </template>
                    </div>
                    <div class="row w-75 mb-3">
                        <div class="form-check col-md-7">
                            <label class="form-check-label">
                                <input type="checkbox" name="auditionIsZoom" :value="true" v-model="values.auditionIsZoom">
                                Would you prefer to interview through Zoom?
                            </label>
                            <p class="form-text mt-2"><small>If you choose Zoom, a link will be sent to the email provided.</small></p>
                        </div>
                    </div>
                    <h5>Additional Documents</h5>
                    <div class="row w-75">
                        <p class="col-12">R&eacute;sum&eacute; required at the time of application. All remaining documents must be received prior to your interview. They can be emailed directly to <a href="mailto:audition@ucf.edu">audition@ucf.edu</a>. Please be sure all documents you send include your first and last name and the type of document (<em>e.g.</em>, r&eacute;sum&eacute;, letter, transcript, etc.).</p>
                        <p class="col-12"><small>Maximum 10 files or 10 MB total.</small></p>
                        <div class="col-md-7">
                            <file-input :name="`resume`" :file="values.files.resume" :acceptedFileTypes="acceptedFileTypes" :index="-1" :required="true" @fileChange="payload => {fileChange(payload)}" @removeFile="payload => {removeFile(payload)}" />
                        </div>
                        <div v-if="values.files.extra.length > 0" class="col-md-7">
                            <file-input v-for="(file, i) in extraFileList" :key="i" :index="i" :required="false" :name="`file-extra-${i}`" :file="file" :acceptedFileTypes="acceptedFileTypes" @fileChange="payload => {fileChange(payload)}" @removeFile="payload => {removeFile(payload)}" />
                        </div>
                    </div>
                    <div class="row w-75 mb-3">
                        <div class="col-md-7">
                            <button type="button" class="btn btn-complementary btn-sm mt-2 rounded float-right" @click="newFile">&plus;</button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6" id="recaptcha-div">
                            <div class="g-recaptcha" :data-sitekey="rcSiteKey"></div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-secondary" @click="submitForm">Submit</button>
                </div>
                <div v-if="showAcceptd" class="mb-3">
                    <p v-html="acceptdMessage" />
                </div>
            </form>
        </div>
        <div v-else class="status-container w-100 rounded" style="min-height: 100px;">
            <p class="text-center" id="status-msg">{{ statusMessage }}</p>
            <progress-bar v-if="isProcessing" :value="uploadPercentage" />
        </div>
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>
