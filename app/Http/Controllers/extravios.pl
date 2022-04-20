test :- write( 'Prolog \nwas called \nfrom PHP \nsuccessfully.' ).

:- dynamic caracteristica/1.
publicacion(5).
publicacion(6).

caracteristica(rojo).
caracteristica(chico).
caracteristica(grande).
caracteristica(transparente).
caracteristica(brilloso).
caracteristica(negro).

etiquetas(5, rojo).
etiquetas(5, chico).
etiquetas(6, negro).
etiquetas(6, brilloso).

exist(Caracteristica):-caracteristica(Caracteristica)->write("simon qlero");asserta(caracteristica(Caracteristica)).
writefacts:-
    open('extavios.pl',write,Out),
    write(Out,'caracteristica(verde)'),
    close(Out);