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

## 4. Delete Detail Activity

### 4.1 Delete Scorm Activity

### 4.2 Delete H5P Activity

### 4.3 Delete Other Activity

#### 4.3.1 File Activity (Resource)
