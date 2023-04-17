```mysql
#删除重复的记录
DELETE FROM nc_data_liansuo
WHERE id NOT IN (
SELECT t.id FROM
( SELECT MIN(id) as id FROM nc_data_liansuo GROUP BY article_id ) t)
```

