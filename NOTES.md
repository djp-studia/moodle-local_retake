# Sumber Data Course dan Activity

## 1. Delete Course Completion

## 2. Delete Course Activity Completion

```sql
DELETE A
FROM mdl_course_modules A
JOIN mdl_course_modules_completion B ON A.id = B.coursemoduleid
WHERE userid = 3
AND A.course = 2;
```

## 3. Delete User Course Grade

### Current Grade

```sql
DELETE A
FROM mdl_grade_grades A
JOIN mdl_grade_items B ON A.itemid = B.id
WHERE A.userid = 3
AND B.courseid = 2
```

### Grade History

```sql
DELETE A
FROM mdl_grade_grades_history A
JOIN mdl_grade_items B ON A.itemid = B.id
WHERE A.userid = 3
AND B.courseid = 2
```

## 4. Delete Detail Activity

### 4.0 Get Detail Activity


### 4.1 Delete Scorm Activity

```sql
SELECT B.*,
       A.*
FROM mdl_scorm_scoes_track A
JOIN mdl_scorm B ON A.scormid = B.id
WHERE B.course = 2
AND A.userid = 3
```

### 4.2 Delete H5P Activity (MOODLE)

```sql
SELECT C.*,
       B.*,
       A.*
FROM mdl_h5pactivity_attempts_results A
JOIN mdl_h5pactivity_attempts B ON A.attemptid = B.id
JOIN mdl_h5pactivity C ON B.h5pactivityid = C.id
WHERE C.course = 2
AND B.userid = 3
```

### 4.3 Delete Other Activity

#### 4.3.1 File Activity (Resource)

## 5. Delete User Enrollment

```sql
# delete enrollment
SELECT B.*,
       A.*
FROM mdl_user_enrolments A
JOIN mdl_enrol B ON A.enrolid = B.id
WHERE B.courseid = 2
AND A.userid = 2
```

# TODO

- [x] Plugin dapat menghapus seluruh data terkait pembelajaran yang dilakukan oleh pegawai
  - [x] Data Activity SCORM
  - [x] Data Activity H5P/HVP
  - [x] Data Activity Lainnya
- [x] Plugin dapat mengapus seluruh data progress pembelajaran serta activity completion
  - [x] Data Activity SCORM
  - [x] Data Activity H5P/HVP
  - [x] Data Activity Lainnya
- [x] Plugin dapat menghapus seluruh data completion terkait grades
- [ ] Plugin dapat menghapus course completion (aggregat dari activity completion)
  - [ ] Course Completion
  - [ ] Course Completion Criteria and Aggregat
- [ ] Plugin harus bisa disetting dan diakses dengan kondisi dan proses tertentu
  - [ ] Enable on All Course
  - [ ] Enable on Specific Course
  - [ ] Except on Specific Course 
  - [ ] Delete Grade History
- [ ] Plugin harus mampu mencatat riwayat retake course oleh masing-masing pegawai
