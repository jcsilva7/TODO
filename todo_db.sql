create table user
(
    id       bigint auto_increment
        primary key,
    name     varchar(128) not null,
    password varchar(255) not null
);

create table task
(
    id                bigint auto_increment
        primary key,
    name              varchar(128)                 not null,
    description       varchar(255)                 null,
    status            varchar(12)                  not null,
    registration_date datetime default (curtime()) not null,
    expiry_date       datetime                     null,
    tag               varchar(64)                  null,
    user_id           bigint                       null,
    constraint task_ibfk_1
        foreign key (user_id) references user (id)
);

create index User_id
    on task (user_id);


