create database takalo_takalo;
use takalo_takalo;


create table tk_user(
    id_user int auto_increment primary key,
    name varchar(200),
    email varchar(200),
    status varchar(100),
    phone varchar(15),
    join_date date,
    last_active varchar(200),
    pwd varchar(200),
    role ENUM('admin','user') DEFAULT 'user'
);

create table tk_discussion(
    id_discussion int auto_increment primary key,
    title varchar(200),
    id_user1 int,
    id_user2 int,
    date_creation date,
    foreign key (id_user1) references tk_user(id_user),
    foreign key (id_user2) references tk_user(id_user)
);

create table tk_messages(
    id_message int auto_increment primary key,
    id_discussion int,
    id_sender int,
    contenue text,
    date_envoie date,
    seen_at datetime null,
    foreign key (id_discussion) references tk_discussion(id_discussion),
    foreign key (id_sender) references tk_user(id_user)
);

alter table tk_user drop column last_active; 
alter table tk_user add column last_active datetime; 


create table tk_categorie(
    id_categorie int auto_increment primary key,
    nom_categorie varchar(200) not null unique
);

create table tk_objets(
    id_objet int auto_increment primary key,
    id_proprietaire int not null,
    id_categorie int not null,
    title varchar(200),
    description text,
    prix_estime decimal(10,2),
    date_creation datetime,
    foreign key (id_proprietaire) references tk_user(id_user),
    foreign key (id_categorie) references tk_categorie(id_categorie)
);

create table tk_objet_img(
    id_objet_img int auto_increment primary key,
    id_objet int not null,
    image varchar(200) not null,
    foreign key (id_objet) references tk_objets(id_objet)
);


create table tk_echanges(
    id_echange int auto_increment primary key,
    id_proposeur int not null,
    id_receveur int not null,
    objet_proposer int not null,
    objet_requise int not null,
    status ENUM('attente','accepter','refuser') DEFAULT 'attente',
    date_proposition datetime,
    foreign key(id_proposeur) references tk_user(id_user),
    foreign key(id_receveur) references tk_user(id_user),
    foreign key(objet_proposer) references tk_objets(id_objet),
    foreign key(objet_requise) references tk_objets(id_objet)
);

create table tk_objet_history(
    id_objet_history int auto_increment primary key,
    id_objet int not null,
    id_proprietaire int not null,
    id_echange int not null,
    date_echange datetime,
    foreign key(id_objet) references tk_objets (id_objet),
    foreign key(id_proprietaire) references tk_user (id_user),
    foreign key(id_echange) references tk_echanges (id_echange)
);
