There is a problem with the get_entries.php function, the way the SQL is
written $beg <= $created_at <= $end doesn't actually work within the WHERE function

I might need to add two WHERE clauses or do some other type of comparitor 
operation to get it to return only THIS DAYS (84600 seconds) worth of entries
