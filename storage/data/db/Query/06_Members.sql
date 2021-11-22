SELECT
    "Id", "No", "Name", "Email",
    "Gender", "Birthday", "Address", "Tel", "Mobile",
    "JoinDate", "Membership", "Disabled",
    "Notes",
    "CreatedAt", "UpdatedAt"
FROM public."Members"
ORDER BY "Id" ASC
LIMIT 100;