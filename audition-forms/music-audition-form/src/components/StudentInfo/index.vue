<template>
    <div class="row w-75 form-section mb-4" id="student-info">
        <template v-for="(input, i) in inputs">
            <text-input v-if="input.name !== 'address'" :key="i"
                :label="input.label"
                :name="input.name"
                :colWidth="input.colWidth"
                :type="input.type !== undefined ? input.type : null"
                :val="input.val !== undefined ? input.val : null"
                :maxLength="input.maxLength !== undefined ? input.maxLength : null"
                :emitOn="'textInputChange'"
                :valueParent="'studentInfo'"
                :isRequired="input.required"
            />
            <address-block v-else :key="i" :fields="input.fields" />
        </template>
        <div class="form-group col-md-6">
            <label>Pronouns:</label>
            <select name="pronouns" class="form-control" v-model="pronouns" @change="changePronouns" required>
                <option value=""> -- Please Select -- </option>
                <option value="he-him-his">He/Him/His</option>
                <option value="she-her-hers">She/Her/Hers</option>
                <option value="they-them-theirs">They/Them/Theirs</option>
                <option value="other">Other</option>
            </select>
        </div>
        <text-input v-if="pronouns === 'other'" 
            label="If Other, please specify" 
            name="pronounOther" 
            :type="null" 
            emitOn="textInputChange" 
            valueParent="studentInfo"
            :isRequired="true"
        />
    </div>
</template>

<script src="./script.js"></script>

<style lang="scss" src="./style.scss" scoped></style>